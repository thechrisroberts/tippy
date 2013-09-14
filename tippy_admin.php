<?php
if (! function_exists('tippy_options_subpanel')) {
  function tippy_options_subpanel()
  {
  	if (isset($_GET['tippy_updated']) && get_option('tippy_options_updated')) {
    	if ($_GET['tippy_updated'] == "1") {
    		echo '<div class="updated"><p><strong>Your options have been updated.</strong></p></div>';
    	} else {
    		echo '<div class="updated"><p><strong>Something went wrong updating options.</strong></p></div>';
    	}

    	update_option('tippy_options_updated', false);
    }

    // We'll be using Tippy, so output the initialization script
    Tippy::initialize_tippy();
?>

<div class="wrap">
	<h2>Tippy Options</h2>

	You can preview your tooltip <?php echo do_shortcode('[tippy title="with this Tippy link" headertext="Demo Tippy"]This tooltip should show what your tooltips will look like based on the settings specified on this page and the styling in your tippy.css.[/tippy]'); ?>.<br /><br />

	<form method="post" action="<?php echo admin_url('admin.php'); ?>">
		<?php wp_nonce_field('tippy-options', 'tippy_verify'); ?>
		<input type="hidden" name="action" value="tippy-options" />

		<div class="tippyOptionSection">
	        <div class="tippyHeader"><span>Tooltip Trigger</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]Do you want the tooltip to automatically appear when a visitor hovers over the Tippy link, or should they have to click the link first?[/tippy]'); ?>)</div>
	        <div class="tippyOptions">
				<input id="tippy_openTip_hover" name="openTip"  type="radio" value="hover" <?php if (Tippy::getOption('openTip') == "hover") echo "checked" ?> />
					<label for="tippy_openTip_hover">
						Tooltip appears on hover
					</label><br />

				<input id="tippy_openTip_click" name="openTip" type="radio" value="click" <?php if (Tippy::getOption('openTip') == "click") echo "checked" ?> />
					<label for="tippy_openTip_click">
						Tooltip appears on click
					</label>
			</div>

			<div class="tippyHeader"><span>Show/Hide</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]Should the tooltip use a fade in/fade out effect, or should it display and hide with no fade effect?[/tippy]'); ?>)</div>
			<div class="tippyOptions">
	        	<input id="tippy_fadeTip_fade" name="fadeTip"  type="radio" value="fade" <?php if (Tippy::getOption('fadeTip') == "fade") echo "checked" ?> />
					<label for="tippy_fadeTip_fade">
						Tooltip fades in and out
					</label><br />

				<input id="tippy_fadeTip_instant" name="fadeTip" type="radio" value="instant" <?php if (Tippy::getOption('fadeTip') == "instant") echo "checked" ?> />
					<label for="tippy_fadeTip_instant">
						Tooltip displays and hides instantly
					</label>
			</div>

			<div class="tippyHeader"><span>Show Delay</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]Adds a slight delay before displaying the tooltip to prevent popping up tooltips when the mouse moves across the page. Set to 0 to show tooltips immediately.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<label for="tippy_showdelay">
					Show delay: 
				</label>
				<input id="tippy_showdelay" name="showdelay" type="text" size="4" value="<?php echo Tippy::getOption('showdelay'); ?>" /> (in milliseconds)
			</div>
			
			<div class="tippyHeader"><span>Hide Delay</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]If you selected for the tooltip to automatically close, how long should it wait before closing? This allows users to mouse away briefly without the tooltip closing right away. Set to 0 if you want it to immediately close.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<label for="tippy_delay">
					Hide delay: 
				</label>
				<input id="tippy_delay" name="delay" type="text" size="4" value="<?php echo Tippy::getOption('delay'); ?>" /> (in milliseconds; 0 for instant)
			</div>
			
			<div class="tippyHeader"><span>Fade In Speed</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]How long should it take to fade the tooltip in? Set to 0 to fade in instantly (no fade effect).[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<label for="tippy_faderate">
					Fade in speed: 
				</label>
				<input id="tippy_faderate" name="faderate" type="text" size="4" value="<?php echo Tippy::getOption('fadeRate'); ?>" /> (in milliseconds)
			</div>
			
			<div class="tippyHeader"><span>Fade Out Speed</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]How long should it take to fade the tooltip out? Set to 0 to fade out instantly (no fade effect).[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<label for="tippy_hidespeed">
					Fade out speed: 
				</label>
				<input id="tippy_hidespeed" name="hidespeed" type="text" size="4" value="<?php echo Tippy::getOption('hidespeed'); ?>" /> (in milliseconds)
			</div>

			<div class="tippyHeader"><span>Close method</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]Should the tooltip automatically disappear when the visitor mouses away, or should visitors have to manually close the tooltip? (Note, you will need to be sure to specify "Show close links" if you set this to remain sticky.)[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_sticky_auto" name="sticky" type="radio" value="false" <?php if (Tippy::getOption('sticky') === false || Tippy::getOption('sticky') == "false") echo "checked" ?> />
					<label for="tippy_sticky_auto">
						Automatically closes when visitor mouses away from the link or tooltip
					</label><br />
				
				<input id="tippy_sticky_stick" name="sticky"  type="radio" value="true" <?php if (Tippy::getOption('sticky') === true || Tippy::getOption('sticky') == "true") echo "checked" ?> />
					<label for="tippy_sticky_stick">
						Remain visible until visitor manually closes the tooltip
					</label>
			</div>

			<div class="tippyHeader"><span>Close Link</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]Do you want to display a Close link so visitors can manually close the tooltip? If so, what should the Close link say?[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_showClose" name="showClose" type="checkbox" value="true" <?php if (Tippy::getOption('showClose') == true) echo "checked" ?> /> 
					<label for="tippy_showClose">
						Show close link on tooltips
					</label><br /><br />
				
				<label for="tippy_closeLinkText">
					Text to display for the Close link
				</label>
				<input id="tippy_closeLinkText" name="closeLinkText" size="15" type="text" value="<?php echo Tippy::getOption('closeLinkText'); ?>" />
			</div>

			<div class="tippyHeader"><span>Autoload Content</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]Should tooltip content automatically load with the page? Typically this is desirable, but if you have something like audio set to autoplay when the tooltip opens, you might want to turn off autoload.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_autoload" name="htmlentities" type="checkbox" value="false" <?php if (Tippy::getOption('htmlentities') == false) echo "checked" ?> /> 
					<label for="tippy_autoload">
						Autoload Content
					</label>
			</div>
		</div><!-- .tippyOptionSection -->

		<div class="tippyOptionSection">		
			<div class="tippyHeader"><span>Multiple Tooltips</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]By default, Tippy closes an open tooltip before opening a new one. If you check this box, multiple tooltips can be visible at the same time.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_multitip" name="multitip" type="checkbox" value="true" <?php if (Tippy::getOption('multitip') == 'true') echo "checked" ?> /> 
					<label for="tippy_multitip">
						Allow multiple tooltips
					</label>
			</div>
					
			<div class="tippyHeader"><span>Autoshow Tooltips</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]By default, tooltips are hidden until displayed when the user hovers over or clicks a Tippy link. Check this option to have all tooltips visible when the page loads.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_autoshow" name="autoshow" type="checkbox" value="true" <?php if (Tippy::getOption('autoshow') == 'true') echo "checked" ?> /> 
					<label for="tippy_autoshow">
						Tooltips visible by default
					</label>
			</div>
					
			<div class="tippyHeader"><span>Show Header</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]By default, all tooltips will display a header. Uncheck this to turn off the header. This can be overridden per-tooltip.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_showheader" name="showheader" type="checkbox" value="true" <?php if (Tippy::getOption('showheader') == 'true') echo "checked" ?> /> 
					<label for="tippy_showheader">
						Show tooltip header by default
					</label>
			</div>
			
			<div class="tippyHeader"><span>Link Target</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]If you specify a url for your Tippy or Header link, should the url open in a new window or the same window?[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_linkWindow_same" name="linkWindow" type="radio" value="same" <?php if (Tippy::getOption('linkWindow') == "same") echo "checked" ?> />
					<label for="tippy_linkWindow_same">
						Open links in the same window
					</label><br />
				
				<input id="tippy_linkWindow_new" name="linkWindow"  type="radio" value="new" <?php if (Tippy::getOption('linkWindow') == "new") echo "checked" ?> />
					<label for="tippy_linkWindow_new">
						Open links in a new window
					</label>
			</div>
			
			<div class="tippyHeader"><span>Tooltip Location</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]Decide where you want the tooltip to appear. If you specify absolute or fixed, you need to set the x/y offset below or set the offset per-tooltip.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_tipPosition_link" name="tipPosition" type="radio" value="link" <?php if (Tippy::getOption('tipPosition') == "link") echo "checked" ?> />
					<label for="tippy_tipPosition_link">
						Tooltip positioned under the Tippy link
					</label><br />
				
				<input id="tippy_tipPosition_mouse" name="tipPosition"  type="radio" value="mouse" <?php if (Tippy::getOption('tipPosition') == "mouse") echo "checked" ?> />
					<label for="tippy_tipPosition_mouse">
						Tooltip positioned under the mouse pointer
					</label><br />

				<input id="tippy_tipPosition_container" name="tipPosition"  type="radio" value="absolute" <?php if (Tippy::getOption('tipPosition') == "absolute") echo "checked" ?> />
					<label for="tippy_tipPosition_container">
						Tooltip absolute positioned to the containing element.
					</label><br />

				<input id="tippy_tipPosition_fixed" name="tipPosition"  type="radio" value="fixed" <?php if (Tippy::getOption('tipPosition') == "fixed") echo "checked" ?> />
					<label for="tippy_tipPosition_fixed">
						Tooltip in a fixed position.
					</label>
			</div>

			<div class="tippyHeader"><span>Tooltip Offset</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]Specify X/Y offsets if you want to nudge the tooltip around - make it display farther away from its trigger position. Give it negative values to move it up or left, positive values for right or down.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<div style="display: inline-block; width: 100px;">
					<label for="tippy_tipOffsetX">
						Left/right offset
					</label>
				</div>
				<input id="tippy_tipOffsetX" name="tipOffsetX" size="3" type="text" value="<?php echo Tippy::getOption('tipOffsetX'); ?>" /> px<br />
				
				<div style="display: inline-block; width: 100px;">
					<label for="tippy_tipOffsetY">
						Up/down offset
					</label>
				</div>
				<input id="tippy_tipOffsetY" name="tipOffsetY" size="3" type="text" value="<?php echo Tippy::getOption('tipOffsetY'); ?>" /> px
			</div>
			
			<div class="tippyHeader"><span>Title Attribute</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]For SEO and Accessibility best practices, it is best to add a title attribute to link elements. Most browsers display the title as a miniature tooltip (for instance, a small yellow box under the pointer). This can get in the way of Tippy. Specify whether or not you want to include a title in your Tippy links.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
	        	<input id="tippy_showTitle" name="showTitle" type="checkbox" value="true" <?php if (Tippy::getOption('showTitle') == "true") echo "checked" ?> /> 
					<label for="tippy_showTitle">
						Use title attribute in Tippy links
					</label>
			</div>

			<div class="tippyHeader"><span>Draggable Tooltips</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]Uses jQuery UI. Allow users to drag tooltips around. Specify whether dragging should work from the header only or from any part of the tooltip.<br /><br />For best results, set the tooltip to sticky and limit dragging to the header.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
	        	<input id="tippy_dragTips" name="dragTips" type="checkbox" value="true" <?php if (Tippy::getOption('dragTips') == "true") echo "checked" ?> /> 
					<label for="tippy_dragTips">
						Allow draggable tooltips
					</label><br />

				<input id="tippy_dragHeader" name="dragHeader" type="checkbox" value="true" <?php if (Tippy::getOption('dragHeader') == "true") echo "checked" ?> /> 
					<label for="tippy_dragHeader">
						Only drag from the header
					</label>
			</div>
			
			<div class="tippyHeader"><span>Tooltip Container</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]By default, Tippy is located as a child of the body element. If you put a CSS selector here, Tippy will be moved inside that element. Might be useful if you switch position to absolute and set the new container to position: relative since Tippy\'s position will be determined by that element.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<div style="display: inline-block; width: 100px;">
					<label for="tippy_tipContainer">
						Tippy container: 
					</label>
				</div>
				<input id="tippy_tipContainer" name="tipContainer" size="10" type="text" value="<?php echo Tippy::getOption('tipContainer'); ?>" /> Leave empty for default
			</div>
			
			<div class="tippyHeader"><span>Calculate Position</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" position="link" delay="900" offsetx="0" offsety="10" width="400"]This only has an effect when position is calculated relative to the Tippy link. By default, position of the Tippy link is calculated relative to its parent. In some situations, (when changing container or doing other more advanced positioning options) you may want to get the link position relative to the document.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_calcpos_parent" name="calcpos" type="radio" value="parent" <?php if (Tippy::getOption('calcpos') == "parent") echo "checked" ?> />
					<label for="tippy_calcpos_parent">
						Relative to the parent
					</label><br />
				
				<input id="tippy_calcpos_document" name="calcpos" type="radio" value="document" <?php if (Tippy::getOption('calcpos') == "document") echo "checked" ?> />
					<label for="tippy_calcpos_document">
						Relative to the document
					</label>
			</div>
		</div>

		<div style="clear: both;">&nbsp;</div>

		<input class="button button-primary" type="submit" name="info_update" value="Update Options" />
	</form>
	
	<div class="tippyOptionLabel">How to use Tippy</div>
	<div class="tippyHelpSection">
		<div class="tippyHeader"><span>Typical</span></div>
        <div class="tippyOptions">[tippy title="example tooltip" href="http://croberts.me/"]This is my nifty tooltip![/tippy]</div><br />
        
        <div class="tippyHeader"><span>Specify width and height (in pixels)</span></div>
        <div class="tippyOptions">[tippy title="Specifically sized" height="100" width="450"]This is my specifically sized tooltip.[/tippy]</div><br />

        <div class="tippyHeader"><span>Use an image as the trigger</span></div>
        <div class="tippyOptions">[tippy img="http://example.com/images/nifty_picture.jpg"]This tooltip is triggered by a picture.[/tippy]</div><br />
        
		Find many more examples at the <a href="http://croberts.me/projects/wordpress-plugins/tippy-for-wordpress/">Tippy page</a>.
	</div>
</div>

<?php
	echo Tippy::insert_tippy_content('');
?>

<?php
  }
}
?>