=== Tippy ===
Contributors: Columcille
Donate link: http://croberts.me/
Tags: tooltip, popup
Requires at least: 2.5
Tested up to: 3.6.1
Stable tag: 6.2.1
License: MIT
License URI: https://github.com/jquery/jquery-color/blob/2.1.2/MIT-LICENSE.txt

Allows users to turn text into a tooltip or popup using a special [tippy] tag.

== Description ==

This plugin allows users to create custom popups or tooltips in their posts. The style and behavior of the tooltip are highly configurable through CSS and through the WordPress dashboard.

== Installation ==

Upload the plugin into your wp-content/plugins directory
Activate the plugin through the dashboard
Visit the Tippy Options page under the Settings section of your dashboard.

To customize the tooltip styling, copy plugins/tippy/tippy.css to your theme folder and modify as you wish. Tippy will look for tippy.css in your theme folder before it tries to load its own copy.

To use Tippy, just place Tippy tags wherever you want in your post. All of the attributes are optional except for title (or img) and the text between the shortcode tags. Example:
[tippy title="I am a tooltip!" class="myclass" showheader="true" href="http://croberts.me/" width="450" height="200"]When you hover over a Tippy link, you will get a popup.[/tippy]

== Screenshots ==

1. Tippy in action!

== Upgrade Notice ==

Tippy 6.x represents a major upgrade over previous versions but should remain fully backwards compatible. Please report any bugs that may be encountered.

== Shortcode Attributes ==

The following options can all be included as attributes in your shortcode. Default values, when applicable, are shown in parenthesis. Multiple possible values are listed in parenthesis with the default first.

Note that even though some of the attributes have changed from older versions, the old attributes should continue to work. You will not need to update shortcodes that use the old names.

* autoshow (false/true): If true, the tooltip or tooltips will automatically show when the page loads.
* showtitle (false/true): Whether or not to use the title attribute in links. Good for accessibility, bad for visibility.
* hoverpopup (true/false): If true, tooltip displays when hovering over the link. If false, tooltip displays when the link is clicked.
* showdelay (100): Adds a slight delay before displaying the tooltip to prevent popping up tooltips when the mouse moves across the page.
* showspeed (200): How long it takes in ms for the tooltip to fade in.
* hidespeed (200): How long it takes in ms for the tooltip to fade out.
* autoclose (true/false): Whether or not the tooltip should automatically close after a delay.
* hidedelay (1000): How long it takes in ms before the tooltip begins to auto fade out.
* container: By default, the tooltip is placed in the DOM right where you set it but if you want to use Tippy for specific positioning, you can change its parent element by specifying a CSS selector here.
* position (link/mouse/css position value): Specifies where the tooltip should be positioned. If set to link or mouse, the x and y values are automatically determined.
* top: Useful when position set to fixed, absolute, or relative.
* bottom: Useful when position set to fixed, absolute, or relative.
* left: Useful when position set to fixed, absolute, or relative.
* right: Useful when position set to fixed, absolute, or relative.
* offsetx (10): Set a default horizontal offset for the tooltip position. Useful when position set to link or mouse.
* offsety (10): Set a default vertical offset for the tooltip position. Useful when position set to link or mouse.
* width: Specify a width for the tooltip. The default is set in jquery.tippy.css.
* height: Specify a height for the tooltip. The default is set in jquery.tippy.css.
* draggable (false/true): Allow visitors to drag the tooltip around. Useful when autoclose is false. Requires jQuery UI.
* dragheader (true/false): If draging is enabled and this is set to true, tooltip will only be draggable from the header bar. If false, visitors can drag from any part of the tooltip.
* anchor: Optional CSS selector specifying the link element that will trigger Tippy.
* title: Sets the text shown on the link and in the header.
* swaptitle: Alternate title to use when the visitor hovers over the link.
* img: Tippy can be used with images rather than text; set img to the url of an image to display Tippy on an image.
* swapimg: Like swaptitle, swapimg switches to an alternate image when a visitor hovers over the link.
* href: If href is set, the Tippy link will point to this url.
* target: Specifies the link target for the Tippy link.
* showheader (true/false): Whether or not the tooltip should show a header.
* headertitle: By default, the header is set to the tooltip title. With headertitle, you can set specific text for the title.
* headerhref: If headerhref is set, the tooltip header text will be a link pointing to this url.
* showclose (true/false): Whether or not the tooltip have a close link. Usefor for mobile devices or when autoclose is false.
* closetext ('close'): The text to display for the close link.
* calcpos ('parent/document'): Calculate the tooltip link position relative to its parent or to the whole document.
* alttrigger: Specify an element to use as additional triggers for the tooltip. Be sure to include . or # - ie: .triggerClass or #triggerId.

== Changelog ==

= 6.2.1 =
* Fixed a glitch where mousing out of an alttrigger element wouldn't trigger hiding the tooltip

= 6.2.0 =
* Added the new alttrigger attribute to allow triggering the tooltip on custom links and buttons.

= 6.1.3 =
* Adjusted subtips to increase max number of tips to somewhere around 640k, which should be enough for anyone.

= 6.1.2 =
* Fixed an issue with subtips having line breaks
* Change the method used for swapping images; preserves size of the initial image and prevents any jumps
* Tells mediaelement.js to load after tooltips are displayed

= 6.1.1 =
* Fixes a text overflow issue
* Fix autoshow setting for content in shortcodes

= 6.1.0 =
* Filters tooltip text through force_balance_tags() to ensure tags are balanced
* Add support for nested tooltips. To add a nested tooltip, use the shortcode [subtippy] inside [tippy]. Multiple levels can be added by using a number: [subtippy1]...[/subtippy1], etc.
* Fixed a glitch with the initial jQuery load

= 6.0.7 =
* Tweak to allow shortcode to work in sidebar widgets.

= 6.0.6 =
* Added a new position option in the dashboard

= 6.0.5 =
* Updated the position calculation

= 6.0.4 =
* Tippy tweaks to improve IE8 support

= 6.0.3 =
* Restored deprecated classes for old stylesheets

= 6.0.2 =
* API update

= 6.0.1 =
* Fixing a glitch on the sticky setting

= 6.0.0 =
* Replaced original tippy.js with the new jquery.tippy.js https://github.com/thechrisroberts/jquery-tippy
* Major updates to tippy.php to work with the new script while maintaining backwards compatibility

= 5.3.2 = 
* Fixed a styling quirk with images

= 5.3.1 =
* Hopefully this fixes the image problems introduced in 5.3.0

= 5.3.0 =
* Adds a new swapimg attribute which makes it easy to swap images on hover, similar to swaptitle

= 5.2.2 = 
* Fixed a bug which prevented Tippy from passing in default settings

= 5.2.1 =
* Fix for default container

= 5.2.0 =
* Added new position options for absolute and fixed position.
* Per-tooltip position attribute. Values: link, mouse, absolute, or fixed
* Added new container option to specify a css selector which should be the parent of the tooltip
* Per-tooltip container attribute, same purpose as new container option
* Per-tooltip method attribute; specify embed or append to determine how content is added to the tooltip. Embed is the traditional way; append uses the new experimental (yet soon to be default) method.

= 5.1.2 =
* Additional tweaks for the experimental method

= 5.1.1 =
* Fixed some glitches with new experimental method
* Added new 'name' attribute for getLink()
* Tweaked method for handling shortcodes embedded in Tippy text

= 5.1.0 =
* Adds a new method for putting text into Tippy. This change takes place behind the scenes but dramatically changes the way Tippy gets populated. Should allow Tippy to work with pretty much any content. The change is not automatic - check Tippy settings and look for the experimental toggle.
* Changed default Close text to X
* Various new internal class methods.

= 5.0.2 =
* Tooltip body contents is removed when tooltip closes.

= 5.0.1 =
* Fixed issue with close link ignoring the show/hide setting.
* Added new target attribute, allows per-tooltip overriding of the global setting. Typically would be target="_self" or target="_blank".

= 5.0.0 =
* Should be backwards-compatible, but please let me know of any issues
* Add new options for draggable tooltips
* Option specifies if tooltips should be dragged from the header or from any part of the tooltip
* Fixed issue with Close link not displaying the right text
* Renamed .js and .css files to use the tippy name: tippy.css, tippy.js
* Added tippy class names to the tooltip
* Adjustments to the default tooltip styling to improve how it matches the site's styling
* Major adjustments to tippy.php, code is now object-oriented
* Improvements to option page styling

= 4.3.0 =
* Adds a new option to specify delay time before the tooltip closes when the visitor mouses away
* Adds a new option to specify the fade rate, how long it takes the tooltip to fade in and out

= 4.2.2 =
* Fixed a glitch with swaptitle when mousing from one tippy link to another
* Fixed a glitch with autoclose that kept it from being sticky when mousing out

= 4.2.1 =
* Fix a glitch with the autoclose="true" setting

= 4.2.0 =
* Adds the autoclose shortcode: per-tooltip setting if the tooltip should auto close when mouse out.
* Adds the swaptitle shortcode: change the Tippy link text to swaptitle when hovered.
* Adds the delay shortcode: allows specifying per-tooltip delay before fading out.

= 4.1.4 =
* Improves how Tippy handles null values

= 4.1.3 =
* Minor update

= 4.1.2 =
* Fixed bug that caused Tippy to ignore the href and reference attributes.
* Fixed bug that ignored headerhref
* Fixed bug that prevented the plugin from loading dom_tooltip.css in the theme folder
* Minor additional enhancements

= 4.1.0 =
* Added two new shortcode attributes: headertext and img
  * headertext By default, the Tippy header shows your title value, which also shows on the Tippy link. headertext lets you specify a different text string for the header.
  * img provides an easy mechanism to add Tippy to trigger on an image rather than a text link. Just put the url to the image and Tippy handles the rest.
   
= 4.0.2 =
* Fixed bug that showed header even when set off.

= 4.0.0 =
* Added new shortcode attributes offsetx and offsety to specify per-tooltip offsets
* Added new shortcode attributes class and id to specify custom class and id values for both the tooltip links and the popups. Previously could only specify a class for the links.
  * Note that the tooltip class and id will append _tip to whatever you specify, so id="myTip" will set myTip on the link and myTip_tip on the tooltip itself.
* Renamed default css from dom_tooltip.factory.css to dom_tooltip.css
* Futher improved dom_tooltip.js to simplify using Tippy directly
* Added a new function on the php side to simplify creating Tippy links
* Fixed a few minor glitches

= 3.9.1 =
* Tweaked stylesheet loading so it will load for the admin page

= 3.9.0 =
* Added new option to specify Close link text
* Added ability to nest shortcodes within Tippy. Note that while Tippy now supports nested shortcodes, not all shortcodes will work properly within Tippy. Output may be different than expected or may show nothing at all.

= 3.8.1 =
* Fix for certain punctuation in the text

= 3.8.0 =
* Internal JavaScript changes
* Added the ability to copy the css to your theme's folder and have Tippy use that for styling
* Style modifications. Ie, new rule for tips with no header.
* Removed some older functions: domTip_setupTip()

= 3.7.5 = 
* Tweak for how position is calculated; added for the Annie plugin. Should continue to be cross-browser compatible, but let me know if problems are observed.

= 3.7.4 =
* Fix for those not wanting to show a header on the tooltip

= 3.7.3 =
* Minor tweak to default CSS, adding a z-index to the tooltip.

= 3.7.2 =
* Fixed a glitch that caused the Tippy link to always be an active link
* Added the function tippy_format_title

= 3.7.1 =
* Switch shortcode to make use of WordPress shortcode API 

= 3.7.0 =
* Added a new parameter to Tippy.loadTipInfo() for passing in a title
* Added the ability to generate the tooltip title from a manually coded title attribute

= 3.6.4 =
* Tweak to fix title for older hard coded links

= 3.6.3 =
* Some tweaks for Glossy compatibility

= 3.6.2 =
* Loads Tippy settings in admin dashboard
 
= 3.6.1 =
* Fixed a bug causing the tooltip not to work.

= 3.6.0 = 
* Added new attribute for a custom class on the tooltip links
* Minor glitches fixed
 
= 3.5.2 = 
* Fixed a backwards-compatibility issue
* Fixed a glitch with the close link on tooltips that don't have a header
 
= 3.5.1 =
* Fixed a glitch that caused an issue with Firefox
* Added option to make tooltips sticky
* Added option to make tooltip links and header links open in a new window
* A few refinements on the Close link
 
= 3.5.0 = 
* Several internal changes making Tippy more accessible to other plugins
* New optional Close link, giving mobile users a way to dismiss the tooltip

= 3.4.1 =
Make use of jQuery.noConflict() to avoid some errors

= 3.4.0 =
Fixed a glitch when a tooltip has headers off

Added height and width attributes to allow per-tooltip size customization.

= 3.3.3 =
Renamed dom_tooltip.css to dom_tooltip.factory.css, using dom_tooltip.css for user customized stylesheets which will not be overwritten when the tooltip is updated.

= 3.3.2 = 
Internally, Tippy now relies on jQuery to calculate positioning data and to manipulate the tooltip.

Added options to give blog administrator greater control over where the tooltip will be positioned.
