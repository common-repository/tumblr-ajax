/*  Copyright 2014  Humphrey Aaron  (email : humphreyaaron7@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


function tumblr_ajax_validatestring(str)
{
	if (str===undefined) return "";
  	if(!!str){
		
		return str;
	}
	return "";
}

function encode_html_entities(rawStr) {
    var encodedStr = rawStr.replace(/[\u00A0-\u9999<>\&]/gim, function (i) {
        return '&#' + i.charCodeAt(0) + ';';
    });
    return encodedStr;
}

// allow can be a string like '<b><i>'
function tumblr_ajax_striptags(str, allow) {
	if(!!str){

		  // making sure the allow arg is a string containing only tags in lowercase (<a><b><c>)
		  allow = (((allow || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

		  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
		  var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
		  return str.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
			return allow.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
		  });
	}
	return str;
}

function tumblr_ajax_getexcerpt(str, strLength)
{
	if(!!str){
		return str.substr(0,strLength);
	}
	return str
}


//function to load JSON return data
function tumblr_ajax_load(returned_data)
{
	var resultHTML = "";
	var showErrors = document.getElementById('tumblr_ajax_sel_errors').value;
	
	try {
	   
			var $ = jQuery;
			var container = $('#tumblr_ajax_data').removeClass('running_ajax');
			var postCount =  document.getElementById('tumblr_ajax_sel_posts_count').value;
			var postLink =  document.getElementById('tumblr_ajax_sel_post_link').value;
			var postShowFirstImage =  document.getElementById('tumblr_ajax_sel_showfirstimage').value;
			var postFirstPhotoSize =  document.getElementById('tumblr_ajax_sel_first_photo_size').value;
			var postPhotoSize =  document.getElementById('tumblr_ajax_sel_load_photo_size').value;
			var postCreateExcerpt =  document.getElementById('tumblr_ajax_sel_create_excerpt').value;
			var postExerptCount =  document.getElementById('tumblr_ajax_sel_excerpt_count').value;
			var postStripHTML =  document.getElementById('tumblr_ajax_sel_strip_htmlALL').value;
			var postAllowHTML =  document.getElementById('tumblr_ajax_sel_allow_HTML').value;
			var postTypes =  document.getElementById('tumblr_ajax_sel_types').value;
			var postVideosize =  document.getElementById('tumblr_ajax_sel_videosize').value;

			for (var i in returned_data.response.posts)
			{
			    var data_array = returned_data.response.posts[i];
			    var post_url = data_array.post_url; 
				var post_type = data_array.type;
				var post_title, post_body;
				
				if (postTypes.indexOf(post_type) != -1)
				{
		
							switch(post_type)
							{
								case "text":
									post_title = tumblr_ajax_validatestring(data_array.title);
									post_body = tumblr_ajax_validatestring(data_array.body);
								  break;
								case "link":
									post_title = tumblr_ajax_validatestring(data_array.title);
									post_body = tumblr_ajax_validatestring(data_array.url);
									post_body += tumblr_ajax_validatestring(data_array.description);
								  break;
								case "quote":
									post_title = "";
									post_body = '<div class="tumblr_ajax_quote"><blockquote>' + tumblr_ajax_validatestring(data_array['text']) + '</blockquote></div>';
									post_body += '<div class="tumblr_ajax_quotesource">' + tumblr_ajax_validatestring(data_array['source']) + '</div>';
								  break;
								case "photo":
									post_title = "";
									post_body = '<div id="tumblr_ajax_photo_' + i + '" class="tumblr_ajax_photo">';

									pNo = 0; //default
									if (postPhotoSize == '500') pNo = '1';
									if (postPhotoSize == '400') pNo = '2';
									if (postPhotoSize == '250') pNo = '3';
									if (postPhotoSize == '100') pNo = '4';
									if (postPhotoSize == '75') pNo = '5';

									post_body += '<img src="' + tumblr_ajax_validatestring(data_array.photos[0].alt_sizes[pNo].url) + '"/>';
									post_body += tumblr_ajax_validatestring(data_array['caption']) + '</div>';								
								  break;
								case "chat":
								    post_title = tumblr_ajax_validatestring(data_array.source_title);
								   
									var conversation_data = data_array.dialogue;									
									for (var k in conversation_data)
									{
									    post_body += '<div class="tumblr_ajax_convoline"><strong>' + conversation_data[k].label + "</strong>" + encode_html_entities(conversation_data[k].phrase) + "<div>";
									}
								  break;
								case "video":
									post_title = tumblr_ajax_validatestring(data_array.caption);
									post_body = '<div class="tumblr_ajax_videosource">' + tumblr_ajax_validatestring(data_array.video_url) + '</div>';

									var videoPlayer = "";
									vNo = 0; //default
									if (postVideosize == '400') vNo = 1;
									if (postVideosize == '500') vNo = 2;

								    videoPlayer = tumblr_ajax_validatestring(data_array.player[vNo].embed_code);
									
									post_body += '<div class="tumblr_ajax_video">' + videoPlayer + '</div>';
								  break;
								case "audio":
									post_title = tumblr_ajax_validatestring(data_array['caption']);
									post_body = '<div class="tumblr_ajax_audio">' + tumblr_ajax_validatestring(data_array['player'])+ '</div>';
								  break;
								case "answer":
									post_title = tumblr_ajax_validatestring(data_array['question']);
									post_body = tumblr_ajax_validatestring(data_array['answer']);
								  break;
								default:
									post_title = "";
									post_body = "";

							}


							//strip html tags
							var content = post_body;							
							if (postStripHTML=='true')
							{
								//no need to strip of audio, video, photo and link posts
								if (post_type!=="audio" && post_type!=="video" && post_type!=="photo" && post_type!=="link")
								{
									var allowHTMLWhileStripping = "<div><blockquote><strong>"; //default; needed.
									allowHTMLWhileStripping += postAllowHTML;
									content = tumblr_ajax_striptags(post_body, allowHTMLWhileStripping);
								}
							}
							

							if (postCreateExcerpt == 'true')
							{
								//no need to create excerpt of audio, video, photo and link posts
								if (post_type!=="audio" && post_type!=="video" && post_type!=="photo" && post_type!=="link")
								{

									//excerpt
									var excerpt = tumblr_ajax_getexcerpt(content, postExerptCount);

									//create Read More link on excerpt
									if (excerpt.length)
									{
										excerpt+= '... <a href="' + post_url + '" target="_blank">Read More</a>';
									}
									content = excerpt;
								}
							}
	
							var textbefore_title = '<div id="tumblr_ajax_post'  + "" +  i + "" +  '" class="tumblr_ajax_post">';
							var textafter_post_body = '</div>';
         
							if (postLink=='true')
							{
								post_title = '<a href="'  + "" +  post_url  + "" +  '" title="" target="_blank">' + "" + post_title + "" + '</a>';
							}
								
							resultHTML += textbefore_title  + "" +  '<h3>' + post_title + '</h3>';
							resultHTML += '<p>'  +  content  +  '</p>';

						
							if (postShowFirstImage=='true')
							{
								//first image highlight/feature
							    //var first_image = $(post_body).find("img:first").attr("src");
							    var first_image = data_array.trail[0].blog['theme']['header_image'];							   
								if (first_image) {
									resultHTML += '<div class="tumblr_ajax_post_container_firstimage" style="width:' + postFirstPhotoSize + '">';
									resultHTML += '<img class="tumblr_ajax_post_firstimage" style="display:block;width:100%" src="' + "" + first_image + "" + '" title="Tumblr Photo" alt="Tumblr Photo"/>';
									resultHTML += '</div>';
								}
							}

							resultHTML += textafter_post_body + '&nbsp;';								
			
							if (i==(postCount-1)) break;
	
					} //end check if post type is included
			} //end for loop

	}//end try
	catch(err)	
		{
			if (showErrors=='true') {
			  resultHTML="<b>There was an error with Tumblr AJAX.</b><br\>";
			  resultHTML+="Error description:<br\> " + err.message + "\n\n";
			 }
		}

		container.append(resultHTML);
}

