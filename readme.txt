=== Tippy ===
Contributors: Columcille
Donate link: http://croberts.me/
Tags: tooltip, popup
Requires at least: 2.5
Tested up to: 3.5.1
Stable tag: 5.1.0

Allows users to turn text into a tooltip or popup using a special [tippy] tag.

== Description ==

This plugin allows users to create custom popups or tooltips in their posts. The style and behavior of the tooltip are highly configurable through CSS and through the WordPress dashboard.

== Installation ==

Upload the plugin into your wp-content/plugins directory
Activate the plugin through the dashboard
Visit the Tippy Options page under the Settings section of your dashboard.

To customize the tooltip styling, copy plugins/tippy/tippy.css to your theme folder and modify as you wish. Tippy will look for tippy.css in your theme folder before it tries to load its own copy.

To use Tippy, just place Tippy tags wherever you want in your post. All of the attributes are optional except for title (or img) and the text between the shortcode tags. Example:
[tippy title="I am a tooltip!" class="myclass" header="on" href="http://croberts.me/" width="450" height="200"]When you hover over a Tippy link, you will get a popup.[/tippy]

== Screenshots ==

1. Tippy in action!

== Changelog ==

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
