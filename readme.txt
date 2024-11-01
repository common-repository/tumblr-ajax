=== Tumblr Ajax ===
Contributors: humphreyaaron, Aris Blevins
Tags: tumblr, AJAX, javascript, jQuery, pictures, images, widget, sidebar, display, stylish, compact
Requires at least: 2.8
Tested up to: 4.3.1
Stable tag: 1.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Display Tumblr posts via AJAX / Javascript / Client-side HTML requests

== Description == 
Get/display Tumblr posts via AJAX / Javascript / Client-side HTML requests. This plugin is great where the WordPress hosting server does not allow external HTTP requests, or where external HTTP requests on the server are preferred to be minimal.

**Tumblr Ajax Features:**

* Display Tumblr posts, photos, videos and audios in a sidebar, post, or page
* Styles to allow for customization
* Widget options


**Quick Start Guide:**

1. After installing the Tumblr Ajax plugin on your WordPress site, make sure it is activated by logging into your admin area and going to Plugins in the left menu.
2. Before using the plugin, you must authorize your WordPress website to access your Tumblr account by adding the Widget to an existing Sidebar, and configuring it accordingly.
3. Play around with the various styles and options to find what works best for your site.
4. In order to change the CSS styles of Tumblr Posts, you can over-ride, using your own Stylesheet, the plugin's default.css mark-up styles

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload plugin contents to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In the Widgets administration, place the Tumblr AJAX widget on an existing sidebar, and configure the settings accordingly
4. Ensure your theme supports sidebars and widgets

== Frequently Asked Questions ==

= My theme does not support Sidebars =

In your theme's functions.php, you can use register_sidebar function to add a custom sidebar.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.jpg. 

== Changelog ==

= 1.2 =
* Updated to work with current version of WordPress (4.3) // humphreyaaron

= 1.1 =
* Resolved PHP and javascript errors causing the plugin to fail in 3.9.1, updated Tumblr API url, removed donate link for this version // AB

= 1.0 =
* Original/Initial version