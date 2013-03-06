<?php
if (! function_exists('tippy_options_subpanel')) {
  function tippy_options_subpanel()
  {
  	global $tippy;

    if (isset($_GET['tippy_updated']) && get_option('tippy_options_updated')) {
    	if ($_GET['tippy_updated'] == "1") {
    		echo '<div class="updated"><p><strong>Your options have been updated.</strong></p></div>';
    	} else {
    		echo '<div class="updated"><p><strong>Something went wrong updating options.</strong></p></div>';
    	}

    	update_option('tippy_options_updated', false);
    }

    // We'll be using Tippy, so output the initialization script
    $tippy->initialize_tippy();
?>

<div class="wrap">
	<h2>Tippy Options</h2>

	You can preview your tooltip <?php echo do_shortcode('[tippy title="with this Tippy link" headertext="Demo Tippy"]This tooltip should show what your tooltips will look like based on the settings specified on this page and the styling in your tippy.css.[/tippy]'); ?>.<br /><br />

	<form method="post" action="<?php echo admin_url('admin.php'); ?>">
		<?php wp_nonce_field('tippy-options', 'tippy_verify'); ?>
		<input type="hidden" name="action" value="tippy-options" />

		<div class="tippyOptionSection">
	        <div class="tippyHeader"><span>Tooltip Trigger</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]Do you want the tooltip to automatically appear when a visitor hovers over the Tippy link, or should they have to click the link first?[/tippy]'); ?>)</div>
	        <div class="tippyOptions">
				<input id="tippy_openTip_hover" name="openTip"  type="radio" value="hover" <?php if ($tippy->getOption('openTip') == "hover") echo "checked" ?> />
					<label for="tippy_openTip_hover">
						Tooltip appears on hover
					</label><br />

				<input id="tippy_openTip_click" name="openTip" type="radio" value="click" <?php if ($tippy->getOption('openTip') == "click") echo "checked" ?> />
					<label for="tippy_openTip_click">
						Tooltip appears on click
					</label>
			</div>

			<div class="tippyHeader"><span>Show/Hide</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]Should the tooltip use a fade in/fade out effect, or should it display and hide with no fade effect?[/tippy]'); ?>)</div>
			<div class="tippyOptions">
	        	<input id="tippy_fadeTip_fade" name="fadeTip"  type="radio" value="fade" <?php if ($tippy->getOption('fadeTip') == "fade") echo "checked" ?> />
					<label for="tippy_fadeTip_fade">
						Tooltip fades in and out
					</label><br />

				<input id="tippy_fadeTip_instant" name="fadeTip" type="radio" value="instant" <?php if ($tippy->getOption('fadeTip') == "instant") echo "checked" ?> />
					<label for="tippy_fadeTip_instant">
						Tooltip displays and hides instantly
					</label>
			</div>

			<div class="tippyHeader"><span>Fade time</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]If you want to display a fade effect, how long should the fade effect last? A higher value means the tooltip will take longer to fade in/out.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<label for="tippy_faderate">
					Fade rate: 
				</label>
				<input id="tippy_faderate" name="faderate" type="text" size="4" value="<?php echo $tippy->getOption('fadeRate'); ?>" /> (in milliseconds)
			</div>

			<div class="tippyHeader"><span>Close method</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]Should the tooltip automatically disappear when the visitor mouses away, or should visitors have to manually close the tooltip? (Note, you will need to be sure to specify "Show close links" if you set this to remain sticky.)[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_sticky_auto" name="sticky" type="radio" value="false" <?php if ($tippy->getOption('sticky') == "false") echo "checked" ?> />
					<label for="tippy_sticky_auto">
						Automatically closes when visitor mouses away from the link
					</label><br />
				
				<input id="tippy_sticky_stick" name="sticky"  type="radio" value="true" <?php if ($tippy->getOption('sticky') == "true") echo "checked" ?> />
					<label for="tippy_sticky_stick">
						Remain sticky until visitor closes
					</label>
			</div>

			<div class="tippyHeader"><span>Disappear Delay</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]If you selected for the tooltip to automatically close, how long should it wait before closing? This allows users to mouse away briefly without the tooltip closing right away. Set to 0 if you want it to immediately close.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<label for="tippy_delay">
					Delay time: 
				</label>
				<input id="tippy_delay" name="delay" type="text" size="4" value="<?php echo $tippy->getOption('delay'); ?>" /> (in milliseconds; 0 for instant)
			</div>

			<div class="tippyHeader"><span>Close Link</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]Do you want to display a Close link so visitors can manually close the tooltip? If so, what should the Close link say?[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_showClose" name="showClose" type="checkbox" value="true" <?php if ($tippy->getOption('showClose') == 'true') echo "checked" ?> /> 
					<label for="tippy_showClose">
						Show close link on tooltips
					</label><br /><br />
				
				<label for="tippy_closeLinkText">
					Text to display for the Close link
				</label>
				<input id="tippy_closeLinkText" name="closeLinkText" size="15" type="text" value="<?php echo $tippy->getOption('closeLinkText'); ?>" />
			</div>
		</div><!-- .tippyOptionSection -->

		<div class="tippyOptionSection">		
			<div class="tippyHeader"><span>Link Target</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]If you specify a url for your Tippy or Header link, should the url open in a new window or the same window?[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_linkWindow_same" name="linkWindow" type="radio" value="same" <?php if ($tippy->getOption('linkWindow') == "same") echo "checked" ?> />
					<label for="tippy_linkWindow_same">
						Open links in the same window
					</label><br />
				
				<input id="tippy_linkWindow_new" name="linkWindow"  type="radio" value="new" <?php if ($tippy->getOption('linkWindow') == "new") echo "checked" ?> />
					<label for="tippy_linkWindow_new">
						Open links in a new window
					</label>
			</div>
			
			<div class="tippyHeader"><span>Tooltip Location</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]Do you want the tooltip to appear relative to the Tippy link or relative to the mouse pointer?[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<input id="tippy_tipPosition_link" name="tipPosition" type="radio" value="link" <?php if ($tippy->getOption('tipPosition') == "link") echo "checked" ?> />
					<label for="tippy_tipPosition_link">
						Tooltip positioned under the Tippy link
					</label><br />
				
				<input id="tippy_tipPosition_mouse" name="tipPosition"  type="radio" value="mouse" <?php if ($tippy->getOption('tipPosition') == "mouse") echo "checked" ?> />
					<label for="tippy_tipPosition_mouse">
						Tooltip positioned under the mouse pointer
					</label>
			</div>
			
			<div class="tippyHeader"><span>Tooltip Offset</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]Specify X/Y offsets if you want to nudge the tooltip around - make it display farther away from its trigger position. Give it negative values to move it up or left, positive values for right or down.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
				<div style="display: inline-block; width: 100px;">
					<label for="tippy_tipOffsetX">
						Left/right offset
					</label>
				</div>
				<input id="tippy_tipOffsetX" name="tipOffsetX" size="3" type="text" value="<?php echo $tippy->getOption('tipOffsetX'); ?>" />px<br />
				
				<div style="display: inline-block; width: 100px;">
					<label for="tippy_tipOffsetY">
						Up/down offset
					</label>
				</div>
				<input id="tippy_tipOffsetY" name="tipOffsetY" size="3" type="text" value="<?php echo $tippy->getOption('tipOffsetY'); ?>" />px
				
			</div>
			
			<div class="tippyHeader"><span>Title Attribute</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]For SEO and Accessibility best practices, it is best to add a title attribute to link elements. Most browsers display the title as a miniature tooltip (for instance, a small yellow box under the pointer). This can get in the way of Tippy. Specify whether or not you want to include a title in your Tippy links.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
	        	<input id="tippy_showTitle" name="showTitle" type="checkbox" value="true" <?php if ($tippy->getOption('showTitle') == "true") echo "checked" ?> /> 
					<label for="tippy_showTitle">
						Use title attribute in Tippy links
					</label>
			</div>

			<div class="tippyHeader"><span>Draggable Tooltips</span> (<?php echo do_shortcode('[tippy title="info" header="off" autoclose="true" delay="900" offsetx="0" offsety="10" width="400"]Uses jQuery UI. Allow users to drag tooltips around. Specify whether dragging should work from the header only or from any part of the tooltip.<br /><br />For best results, set the tooltip to sticky and limit dragging to the header.[/tippy]'); ?>)</div>
			<div class="tippyOptions">
	        	<input id="tippy_dragTips" name="dragTips" type="checkbox" value="true" <?php if ($tippy->getOption('dragTips') == "true") echo "checked" ?> /> 
					<label for="tippy_dragTips">
						Allow draggable tooltips
					</label><br />

				<input id="tippy_dragHeader" name="dragHeader" type="checkbox" value="true" <?php if ($tippy->getOption('dragHeader') == "true") echo "checked" ?> /> 
					<label for="tippy_dragHeader">
						Only drag from the header
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
  }
}
?>