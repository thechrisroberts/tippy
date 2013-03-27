<?php
/*
Plugin Name: Tippy
Plugin URI: http://croberts.me/tippy/
Description: Simple plugin to display tooltips within your WordPress blog.
Version: 5.1.2
Author: Chris Roberts
Author URI: http://croberts.me/
*/

/*  Copyright 2013 Chris Roberts (email : chris@dailycross.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class Tippy {
    // Initialize settings
    private $openTip = 'hover';
    private $fadeTip = 'fade';
    private $tipPosition = 'link';
    private $tipOffsetX = 0;
    private $tipOffsetY = 10;
    private $linkWindow = 'same';
    private $sticky = 'false';
    private $showTitle = true;
    private $showClose = true;
    private $closeLinkText = 'X';
    private $delay = 700;
    private $fadeRate = 200;
    private $dragTips = true;
    private $dragHeader = true;
    private $useDivContent = false;

    private $optionsLoaded = false;
    private $countTips = 0;

    private $tippyContent = array();

    private $tippyObject = '';

    // Initialize everything
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'load_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'load_styles'));
        add_action('wp_head', array($this, 'initialize_tippy'));
        add_shortcode('tippy', array($this, 'shortcode'));

        add_filter('the_content', array($this, 'insert_tippy_content'), 55);

        // Admin tasks
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_action_tippy-options', array($this, 'admin_validate_options'));

        add_filter('plugin_action_links', array($this, 'settings_link'), 10, 2);
    }

    public function settings_link($links, $file) { 
        if ($file == 'tippy/tippy.php') {
            $settings_link = '<a href="options-general.php?page=tippy.php">Settings</a>'; 
            array_push($links, $settings_link);
        }
        
        return $links; 
    }

    private function loadOptions()
    {
        // Grab the Tippy array.
        $optionsArray = get_option('tippy_options', array());

        // If the array is empty, either no options have been saved or the user
        // used an older Tippy before the options array. If we find old options,
        // update the user options.
        if (empty($optionsArray) && get_option('tippy_openTip', false)) {
            $this->updateOutdated();
        } else if (!empty($optionsArray)) {
            // We have options. Load them into the class, overwriting defaults.
            foreach ($optionsArray as $optionName => $optionValue) {
                $this->$optionName = $optionValue;
            }
        } else {
            // No options stored, rely on already set default values. Save them
            // for next time.
            $this->saveOptions();
        }

        $this->optionsLoaded = true;
    }

    private function updateOutdated()
    {
        // Load the old individual options with our predefined defaults as backup
        $this->openTip = get_option('tippy_openTip', $this->openTip);
        $this->fadeTip = get_option('tippy_fadeTip', $this->fadeTip);
        $this->tipPosition = get_option('tippy_tipPosition', $this->tipPosition);
        $this->tipOffsetX = get_option('tippy_tipOffsetX', $this->tipOffsetX);
        $this->tipOffsetY = get_option('tippy_tipOffsetY', $this->tipOffsetY);
        $this->linkWindow = get_option('tippy_linkWindow', $this->linkWindow);
        $this->sticky = get_option('tippy_sticky', $this->sticky);
        $this->showTitle = get_option('tippy_showTitle', $this->showTitle);
        $this->showClose = get_option('tippy_showClose', $this->showClose);
        $this->closeLinkText = get_option('tippy_closeLinkText', $this->closeLinkText);
        $this->delay = get_option('tippy_delay', $this->delay);
        $this->fadeRate = get_option('tippy_faderate', $this->fadeRate);

        // Save options to the new array and delete the old options
        if ($this->saveOptions()) {
            delete_option('tippy_openTip');
            delete_option('tippy_fadeTip');
            delete_option('tippy_tipPosition');
            delete_option('tippy_tipOffsetX');
            delete_option('tippy_tipOffsetY');
            delete_option('tippy_linkWindow');
            delete_option('tippy_sticky');
            delete_option('tippy_showTitle');
            delete_option('tippy_showClose');
            delete_option('tippy_closeLinkText');
            delete_option('tippy_delay');
            delete_option('tippy_faderate');
        }
    }

    private function saveOptions()
    {
        // Store values into our array
        $optionsArray = array('openTip' => $this->openTip,
                              'fadeTip' => $this->fadeTip,
                              'tipPosition' => $this->tipPosition,
                              'tipOffsetX' => $this->tipOffsetX,
                              'tipOffsetY' => $this->tipOffsetY,
                              'linkWindow' => $this->linkWindow,
                              'sticky' => $this->sticky,
                              'showTitle' => $this->showTitle,
                              'showClose' => $this->showClose,
                              'closeLinkText' => $this->closeLinkText,
                              'delay' => $this->delay,
                              'fadeRate' => $this->fadeRate,
                              'dragTips' => $this->dragTips,
                              'dragHeader' => $this->dragHeader,
                              'useDivContent' => $this->useDivContent);

        return update_option('tippy_options', $optionsArray);
    }

    public function getOption($optionName)
    {
        // Check to see if we've already tried to load saved options. If not, load them.
        if (!$this->optionsLoaded) {
            $this->loadOptions();
        }

        if (isset($this->$optionName)) {
            return $this->$optionName;
        } else {
            return false;
        }
    }

    public function register_scripts()
    {
        wp_register_script('Tippy', plugins_url() .'/tippy/tippy.js', array('jquery'), '5.1.0');
    }

    public function load_scripts()
    {
        // Load jQuery, if not already present
        wp_enqueue_script('jquery');
        
        if ($this->dragTips) {
            wp_enqueue_script('jquery-ui-draggable');
        }
        
        // Load the Tippy script
        $this->register_scripts();
        wp_enqueue_script('Tippy');
    }

    public function register_styles()
    {
        // Load the Tippy css. Checks for several possibilities with the css file. Most of these are
        // outdated options, but people might still be using them, so check for them.
        $tippyCSS_locations = array(get_stylesheet_directory() .'/tippy.css' => get_bloginfo('stylesheet_directory') .'/tippy.css',
                                    get_stylesheet_directory() .'/dom_tooltip.css' => get_bloginfo('stylesheet_directory') .'/dom_tooltip.css',
                                    get_stylesheet_directory() .'/dom_tooltip.factory.css' => get_bloginfo('stylesheet_directory') .'/dom_tooltip.factory.css',
                                    WP_PLUGIN_DIR .'/tippy/tippy.css' => plugins_url() .'/tippy/tippy.css');

        foreach ($tippyCSS_locations as $tippyPath => $tippyCSS) {
            if (file_exists($tippyPath)) {
                wp_register_style('Tippy', $tippyCSS);
                
                break;
            }
        }
    }

    public function load_styles()
    {
        $this->register_styles();
        wp_enqueue_style('Tippy');
    }

    public function initialize_tippy()
    {
        if ($this->getOption('fadeTip') == "fade") {
            $tippyFadeRate = $this->getOption('fadeRate');
        } else {
            $tippyFadeRate = 0;
        }

        $useDivContent = ($this->getOption('useDivContent') === true) ? "true" : "false";
        
        echo '
            <script type="text/javascript">
                Tippy.initialize({
                    tipPosition: "'. $this->getOption("tipPosition") .'",
                    tipOffsetX: '. $this->getOption("tipOffsetX") .',
                    tipOffsetY: '. $this->getOption("tipOffsetY") .',
                    fadeRate: '. $tippyFadeRate .',
                    sticky: '. $this->getOption("sticky") .',
                    showClose: '. $this->getOption("showClose") .',
                    closeText: "'. $this->getOption("closeLinkText") .'",
                    delay: '. $this->getOption("delay") .',
                    draggable: '. $this->getOption("dragTips") .',
                    dragheader: '. $this->getOption("dragHeader") .',
                    useDivContent: '. $useDivContent .'
                });
            </script>

        ';
    }

    public function admin_menu()
    {
        require_once(plugin_dir_path(__FILE__) .'/tippy_admin.php');

        $page = add_options_page('Tippy Plugin Options', 'Tippy', 'manage_options', basename(__FILE__), 'tippy_options_subpanel');

        add_action('admin_print_styles-' . $page, array($this, 'admin_load_styles'));
    }

    public function admin_init()
    {
        wp_register_style('TippyAdmin', plugins_url() .'/tippy/tippy_admin.css');
        $this->register_styles();
        $this->register_scripts();
    }

    public function admin_load_styles()
    {
        wp_enqueue_style('TippyAdmin');
        $this->load_styles();
        $this->load_scripts();
    }

    public function admin_validate_options()
    {
        $tippy_validated = "0";

        // Retrieve and save settings
        if (isset($_POST['info_update']) && is_admin() && wp_verify_nonce($_POST['tippy_verify'], 'tippy-options')) {
            $this->openTip = sanitize_text_field($_POST['openTip']);
            $this->fadeTip = sanitize_text_field($_POST['fadeTip']);
            $this->tipPosition = sanitize_text_field($_POST['tipPosition']);
            $this->tipOffsetX = intval($_POST['tipOffsetX']);
            $this->tipOffsetY = intval($_POST['tipOffsetY']);
            $this->linkWindow = sanitize_text_field($_POST['linkWindow']);
            $this->sticky = sanitize_text_field($_POST['sticky']);
            $this->showTitle = isset($_POST['showTitle']) ? true : false;
            $this->showClose = isset($_POST['showClose']) ? "true" : "false";
            $this->closeLinkText = isset($_POST['closeLinkText']) ? sanitize_text_field($_POST['closeLinkText']) : 'X';
            $this->delay = isset($_POST['delay']) ? intval($_POST['delay']) : 900;
            $this->fadeRate = isset($_POST['faderate']) ? intval($_POST['faderate']) : 300;
            $this->dragTips = isset($_POST['dragTips']) ? "true" : "false";
            $this->dragHeader = isset($_POST['dragHeader']) ? "true" : "false";
            $this->useDivContent = isset($_POST['useDivContent']) ? "true" : "false";

            $this->saveOptions();

            $tippy_validated = "1";
        } else if (isset($_POST['info_update'])) {
            $tippy_validated = "0";
        }

        update_option('tippy_options_updated', true);

        wp_redirect(admin_url('options-general.php?page=tippy.php') ."&tippy_updated=$tippy_validated");
    }

    // Pull data out of the shortcode and pass it to format link
    public function shortcode($attributes, $text = '')
    {
        $link = "";
        
        $tippyAtts = shortcode_atts(array('title' => '',
                                          'swaptitle' => '',
                                          'reference' => false, 
                                          'href' => false, 
                                          'target' => false,
                                          'header' => 'on', 
                                          'headerhref' => false,
                                          'headertext' => false,
                                          'autoclose' => true,
                                          'delay' => false,
                                          'class' => false, 
                                          'id' => false, 
                                          'height' => false, 
                                          'width' => false,
                                          'offsetx' => false,
                                          'offsety' => false,
                                          'img' => false), $attributes);
        
        if (!empty($tippyAtts['href'])) {
            $link = $tippyAtts['href'];
        } else if (!empty($tippyAtts['reference'])) {
            $link = $tippyAtts['reference'];
        }
        
        if ($this->getOption('useDivContent') === 'true') {
            $sendTitle = $tippyAtts['title'];
            $sendText = $text;
        } else {
            $sendTitle = $this->format_title($tippyAtts['title']);
            $sendText = $this->format_text(do_shortcode($text));
        }

        $tippyArr = array('title' => $sendTitle,
                          'swaptitle' => $this->format_title($tippyAtts['swaptitle']),
                          'text' => $sendText,
                          'href' => $link,
                          'target' => $tippyAtts['target'],
                          'header' => $tippyAtts['header'],
                          'headerhref' => $tippyAtts['headerhref'],
                          'headertext' => $this->format_title($tippyAtts['headertext']),
                          'autoclose' => $tippyAtts['autoclose'],
                          'delay' => $tippyAtts['delay'],
                          'class' => $tippyAtts['class'],
                          'id' => $tippyAtts['id'], 
                          'item' => $this->countTips,
                          'height' => $tippyAtts['height'],
                          'width' => $tippyAtts['width'],
                          'offsetx' => $tippyAtts['offsetx'],
                          'offsety' => $tippyAtts['offsety'],
                          'img' => $tippyAtts['img']);
        
        $tippyLink = $this->getLink($tippyArr);
        
        $this->countTips++;
        
        return $tippyLink;
    }

    public function format_title($tippy_title)
    {
        $tippy_title = str_replace("\\", "\\\\", $tippy_title);
        $tippy_title = str_replace("'", "&#8217;", $tippy_title);
        
        return $tippy_title;
    }

    public function format_text($tippy_text)
    {
        // Just need to replace a few items so do it manually rather than using a function like htmlentities()
        $tippy_text = str_replace("\n", "", $tippy_text);
        $tippy_text = str_replace("\r", "", $tippy_text);
        $tippy_text = str_replace("'", "&#8217;", $tippy_text);
        $tippy_text = str_replace("\\", "&#92;", $tippy_text);
        $tippy_text = str_replace("(", "&#40;", $tippy_text);
        $tippy_text = str_replace(")", "&#41;", $tippy_text);

        return $tippy_text;
    }

    // Receive necessary data and shape it into a Tippy link
    /* $tippyArray[
     * 'title' => Optional string (either this or img must be set); should first be passed through tippy_format_title($title).
     * 'swaptitle' => Optional string; text to show when hovering over the Tippy link. Does not work with an image.
     * 'img' => Optional string (either this or title must be set); users may want Tippy to trigger on an image rather than text. This should be the full address to the image.
     * 'text' => Required string; should first be passed through tippy_format_text(do_shortcode($text)).
     * 'href' => Optional string; contains link href for the Tippy link, if desired.
     * 'target' => Optional string; contains the link target, usually either _self or _blank. Overrides global setting.
     * 'header' => Optional string (on/off); specifies if the header is on or off.
     * 'headerhref' => Optional string; by default, header link uses the href value; this overrides the link used in the header.
     * 'headertext' => Optional string; by default, the header displays the title value; this specifies the text to show in the header.
     * 'autoclose' => Optional string; by default, uses global setting; allows per-tooltip specification of autoclose.
     * 'class' => Optional string; specify an additional class for the tooltip link. Class gets passed on to the tooltip with _tip appended.
     * 'id' => Optional string; specifies id for the link. Gets passed on to the tooltip with _tip appended.
     * 'name' => Optional string; specifies a name to set on the link.
     * 'item' => Optional string or int; used to automatically create unique identifiers for the tooltips.
     * 'height' => Optional int; manually specify tooltip height in pixels.
     * 'width' => Optional int; manually specify tooltip width in pixels.
     * 'top' => Optional int; specifies top position in pixels.
     * 'left' => Optional int; specifies left position in pixels.
     * 'bottom' => Optional int; specifies bottom position in pixels.
     * 'right' => Optional int; specifies right position in pixels.
     * 'useDiv' => Optional bool; should we use the new method of inserting content?
     * ];
    */
    public function getLink($tippyArray)
    {
        // Initialize values that might default empty
        $tippyTarget = '';
        $tippyMouseOut = '';
        $tippyTitleAttribute = '';
        $tippyLinkClass = 'tippy_link';
        $tippyLinkName = '';

        // Specify default return value. Defaults to the text passed from Tippy.
        // A later check might swap this with the title.
        $returnText = $tippyArray['text'];
        
        // Make sure array values are clean
        $tippyTitle = isset($tippyArray['title']) ? trim($tippyArray['title']) : '';
        $tippySwapTitle = isset($tippyArray['swaptitle']) ? trim($tippyArray['swaptitle']) : '';
        $tippyText = isset($tippyArray['text']) ? trim($tippyArray['text']) : '';
        $tippyHref = isset($tippyArray['href']) ? trim($tippyArray['href']) : false;
        $tippyTarget = isset($tippyArray['target']) ? trim($tippyArray['target']) : '';
        $tippyHeader = isset($tippyArray['header']) ? strtolower(trim($tippyArray['header'])) : false;
        $tippyHeaderHref = isset($tippyArray['headerhref']) ? trim($tippyArray['headerhref']) : false;
        $tippyHeaderText = isset($tippyArray['headertext']) ? trim($tippyArray['headertext']) : false;
        $tippyAutoclose = isset($tippyArray['autoclose']) ? trim($tippyArray['autoclose']) : false;
        $tippyDelay = isset($tippyArray['delay']) ? trim($tippyArray['delay']) : false;
        $tippyClass = isset($tippyArray['class']) ? trim($tippyArray['class']) : false;
        $tippyId = isset($tippyArray['id']) ? trim($tippyArray['id']) : false;
        $tippyName = isset($tippyArray['name']) ? trim($tippyArray['name']) : false;
        $tippyItem = isset($tippyArray['item']) ? trim($tippyArray['item']) : false;
        $tippyHeight = isset($tippyArray['height']) ? $tippyArray['height'] : false;
        $tippyWidth = isset($tippyArray['width']) ? $tippyArray['width'] : false;
        $tippyOffsetX = isset($tippyArray['offsetx']) ? $tippyArray['offsetx'] : false;
        $tippyOffsetY = isset($tippyArray['offsety']) ? $tippyArray['offsety'] : false;
        $tippyImg = isset($tippyArray['img']) ? $tippyArray['img'] : false;
        $tippyUseDiv = isset($tippyArray['useDiv']) ? $tippyArray['useDiv'] : false;
        
        $tippyLinkText = '';
        
        // Check our values and build the Tippy parameters
        if (!empty($tippyTitle)) {
            $returnText = $tippyTitle;
        }
        
        // See if the user specified an img
        if (!empty($tippyImg)) {
            $tippyLinkText = '<img src="'. $tippyImg .'"';
            
            if (!empty($tippyTitle)) {
                $tippyLinkText .= ' alt="'. $tippyTitle .'"';
            }
            
            if (!empty($tippyClass)) {
                $tippyLinkText .= ' class="'. $tippyClass .'_img"';
            }
            
            $tippyLinkText .= ' />';
        } else {
            $tippyLinkText = $tippyTitle;
        }
        
        // Check required values: linktext (either title or img) and text
        if (!empty($tippyText) && !empty($tippyLinkText)) {
            // Set the tooltip id
            if (empty($tippyId)) {
                $tippyId = 'tippy_tip'. $tippyItem .'_'. rand(100, 9999);
            }

            $this->addTippyObjectValue("id", $tippyId, "'%s'");

            // See if we are using the experimental content method
            if ($this->getOption('useDivContent') === 'true' || $tippyUseDiv === true) {
                $this->addContent($tippyTitle, $tippyText, $tippyId);
            } else {
                $this->addTippyObjectValue("title", htmlentities($tippyTitle, ENT_QUOTES, 'UTF-8'), "'%s'");
                $this->addTippyObjectValue("text", htmlentities($tippyText, ENT_QUOTES, 'UTF-8'), "'%s'");
            }

            // Check href and trigger method
            if ($this->openTip == "hover") {
                $activateTippy = "onmouseover";
                
                $tippyHref = trim($tippyHref);
                if (!empty($tippyHref))
                {
                    $tippyHref = 'href="'. $tippyHref .'" ';
                }
            } else {
                $activateTippy = "onmouseup";
            }
            
            // Check the link target
            if (empty($tippyTarget) && $this->linkWindow == "new") {
                $tippyTarget = 'target="_blank" ';
            } else if (!empty($tippyTarget)) {
                $tippyTarget = 'target="'. $tippyTarget .'" ';
            }
            
            // Should Tippy be sticky?
            if ($tippyAutoclose !== 'false' && ($this->sticky == 'false' || $tippyAutoclose == 'true')) {
                $tippyMouseOut = 'onmouseout="Tippy.fadeTippyOut();"';
                $this->addTippyObjectValue("sticky", "false");

            } else {
                $this->addTippyObjectValue("sticky", "true");
            }
            
            // Should the link use the title attribute?
            if ($this->showTitle == 'true' && !empty($tippyTitle)) {
                $tippyTitleAttribute = sprintf('title="%s" ', htmlspecialchars($tippyTitle, ENT_COMPAT, 'UTF-8'));
            }
            
            // See if we have a swap title
            if (!empty($tippySwapTitle)) {
                $this->addTippyObjectValue("swaptitle", $tippySwapTitle, "'%s'");
            }
            
            // Check the header; allow a variety of possibilities
            if ($tippyHeader === true || $tippyHeader === "on" || $tippyHeader === "yes" || $tippyHeader === "true") {
                $this->addTippyObjectValue("header", htmlentities($tippyTitle, ENT_QUOTES, 'UTF-8'), "'%s'");
                
                // Check the header link
                if (!empty($tippyHeaderHref)) {
                    $this->addTippyObjectValue("headerhref", $tippyHeaderHref, "'%s'");
                }
                
                // Check the header text
                if (!empty($tippyHeaderText)) {
                    $this->addTippyObjectValue("headerText", htmlentities($tippyHeaderText, ENT_QUOTES, 'UTF-8'), "'%s'");
                }
            }
            
            // Check width and height
            if ($tippyWidth !== false) {
                $this->addTippyObjectValue("width", (int)$tippyWidth, "%d");
            }
            
            if ($tippyHeight !== false) {
                $this->addTippyObjectValue("height", (int)$tippyHeight, "%d");
            }
            
            // Check the offsets
            if ($tippyOffsetX !== false) {
                $this->addTippyObjectValue("offsetx", (int)$tippyOffsetX, "%d");
            }
            
            if ($tippyOffsetY !== false) {
                $this->addTippyObjectValue("offsety", (int)$tippyOffsetY, "%d");
            }
            
            // Check class/id
            if (!empty($tippyClass)) {
                $tippyLinkClass .= " ". $tippyClass;
                $this->addTippyObjectValue("tippyclass", $tippyClass, "'%s'");
            }

            if (!empty($tippyName)) {
                $tippyLinkName = sprintf('name="%s"', $tippyName);
            }
            
            // Check delay
            if (!empty($tippyDelay)) {
                $this->addTippyObjectValue("delay", (int)$tippyDelay, "%d");
            }
            
            $returnText = sprintf('<a %s id="%s" class="%s" %s %s %s %s="Tippy.loadTip({ %s, event: event });" %s>%s</a>', $tippyLinkName, $tippyId, $tippyLinkClass, $tippyHref, $tippyTarget, $tippyTitleAttribute, $activateTippy, $this->tippyObject, $tippyMouseOut, $tippyLinkText);

            // Clear the object
            $this->tippyObject = '';
        }
        
        return $returnText;
    }

    private function addTippyObjectValue($valueName, $valueSetting, $valueType = '')
    {
        if (!empty($this->tippyObject)) {
            $this->tippyObject .= ', ';
        }

        if (!empty($valueType)) {
            $this->tippyObject .= sprintf($valueName .": ". $valueType, $valueSetting);
        } else {
            $this->tippyObject .= $valueName .": ". $valueSetting;
        }
    }

    public function addContent($contentTitle, $contentText, $contentId)
    {
        // Inline styling from plugins is bad. Unless it isn't. Here, it isn't.
        $newContentDiv = '<div style="display: none;" class="tippy_content_container" id="'. $contentId .'_content"><span class="tippy_title">'. $contentTitle .'</span><div class="tippy_content">'. do_shortcode($contentText) .'</div></div>';

        $this->tippyContent[$contentId] = $newContentDiv;
    }

    public function insert_tippy_content($content)
    {
        $tippyContent = '';

        if (!empty($this->tippyContent)) {
            foreach ($this->tippyContent as $contentId => $contentDiv) {
                $tippyContent .= $contentDiv ."\r\n";
            }
        }

        // Since we've used the content, clear it out.
        $this->tippyContent = array();

        return $content . $tippyContent;
    }
}

$tippy = new Tippy();

/*
 * The following are deprecated or helper functions
 */

if (! function_exists('tippy_formatLink') ) {
    function tippy_formatLink($tippyShowHeader, $tippyTitle, $tippyHref, $tippyText, $tippyCustomClass, $tippyItem, $tippyWidth = false, $tippyHeight = false)
    {
        global $tippy;
        return $tippy->getLink(array('header' => $tippyShowHeader, 'title' => $tippyTitle, 'href' => $tippyHref, 'text' => $tippyText, 'class' => $tippyCustomClass, 'item' => $tippyItem, 'width' => $tippyWidth, 'height' => $tippyHeight));
    }
}

if (! function_exists('tippy_getLink')) {
    function tippy_getLink($tippyArray)
    {
        global $tippy;
        return $tippy->getLink($tippyArray);
    }
}

if (!function_exists('tippy_format_title')) {
    function tippy_format_title($tippy_title)
    {
        global $tippy;
        return $tippy->format_title($tippy_title);
    }
}

if (!function_exists('tippy_format_text')) {
    function tippy_format_text($tippy_text)
    {
        global $tippy;
        return $tippy->format_text($tippy_text);
    }
}

?>