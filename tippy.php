<?php
/*
Plugin Name: Tippy
Plugin URI: http://croberts.me/tippy/
Description: Simple plugin to display tooltips within your WordPress blog.
Version: 5.3.2
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
    private $tipOffsetXUnit = 'px';
    private $tipOffsetYUnit = 'px';
    private $tipContainer = false;
    private $linkWindow = 'same';
    private $sticky = 'false';
    private $showTitle = true;
    private $showClose = true;
    private $closeLinkText = 'X';
    private $delay = 700;
    private $fadeRate = 200;
    private $dragTips = true;
    private $dragHeader = true;

    private $optionsLoaded = false;
    private $countTips = 0;

    private $tippyContent = array();

    private $tippyObject = '';

    // List all options possible for Tippy. Used to verify valid attributes
    // in the shortcode.
    private $tippyOptionNames = array(
                                    'multitip', 
                                    'autoshow', 
                                    'showtitle', 
                                    'hoverpopup', 
                                    'showdelay', 
                                    'showspeed', 
                                    'hidespeed', 
                                    'autoclose', 
                                    'hidedelay', 
                                    'container', 
                                    'position', 
                                    'top', 
                                    'bottom', 
                                    'left', 
                                    'right', 
                                    'offsetx', 
                                    'offsety', 
                                    'width', 
                                    'height', 
                                    'draggable', 
                                    'dragheader', 
                                    'anchor', 
                                    'title', 
                                    'swaptitle', 
                                    'img', 
                                    'swapimg', 
                                    'href', 
                                    'target', 
                                    'showheader', 
                                    'headertitle', 
                                    'headerhref', 
                                    'showclose', 
                                    'closetext');

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
                              // 'tipOffsetXUnit' => $this->tipOffsetXUnit,
                              // 'tipOffsetYUnit' => $this->tipOffsetYUnit,
                              'tipContainer' => $this->tipContainer,
                              'linkWindow' => $this->linkWindow,
                              'sticky' => $this->sticky,
                              'showTitle' => $this->showTitle,
                              'showClose' => $this->showClose,
                              'closeLinkText' => $this->closeLinkText,
                              'delay' => $this->delay,
                              'fadeRate' => $this->fadeRate,
                              'dragTips' => $this->dragTips,
                              'dragHeader' => $this->dragHeader);

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
        wp_register_script('Tippy', plugins_url() .'/tippy/jquery.tippy.js', array('jquery'), '6.0.0');
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
        // Load the Tippy css. Checks for several possibilities with the css file.
        // Includes checks for deprecated names.
        $tippyCSS_locations = array(get_stylesheet_directory() .'/jquery.tippy.css' => get_bloginfo('stylesheet_directory') .'/jquery.tippy.css',
                                    get_stylesheet_directory() .'/tippy.css' => get_bloginfo('stylesheet_directory') .'/tippy.css',
                                    get_stylesheet_directory() .'/dom_tooltip.css' => get_bloginfo('stylesheet_directory') .'/dom_tooltip.css',
                                    get_stylesheet_directory() .'/dom_tooltip.factory.css' => get_bloginfo('stylesheet_directory') .'/dom_tooltip.factory.css',
                                    WP_PLUGIN_DIR .'/tippy/jquery.tippy.css' => plugins_url() .'/tippy/jquery.tippy.css');

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

        if (!$this->getOption('tipContainer') || $this->getOption('tipContainer') == "") {
            $setContainer = '"body"';
        } else {
            $setContainer = '"'. $this->getOption('tipContainer') .'"';
        }
        
        echo '
            <script type="text/javascript">
                jQuery(\'document\').ready(function() {
                    jQuery(\'.tippy\').tippy();
                });
                
                /*
                Tippy.initialize({
                    tipPosition: "'. $this->getOption("tipPosition") .'",
                    tipContainer: '. $setContainer .',
                    tipOffsetX: '. $this->getOption("tipOffsetX") .',
                    tipOffsetY: '. $this->getOption("tipOffsetY") .',
                    fadeRate: '. $tippyFadeRate .',
                    sticky: '. $this->getOption("sticky") .',
                    showClose: '. $this->getOption("showClose") .',
                    closeText: "'. $this->getOption("closeLinkText") .'",
                    delay: '. $this->getOption("delay") .',
                    draggable: '. $this->getOption("dragTips") .',
                    dragheader: '. $this->getOption("dragHeader") .'
                });
                */
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
            // $this->tipOffsetX = sanitize_text_field($_POST['tipOffsetXUnit']);
            // $this->tipOffsetY = sanitize_text_field($_POST['tipOffsetYUnit']);
            $this->tipContainer = isset($_POST['tipContainer']) ? sanitize_text_field($_POST['tipContainer']) : false;
            $this->linkWindow = sanitize_text_field($_POST['linkWindow']);
            $this->sticky = sanitize_text_field($_POST['sticky']);
            $this->showTitle = isset($_POST['showTitle']) ? true : false;
            $this->showClose = isset($_POST['showClose']) ? "true" : "false";
            $this->closeLinkText = isset($_POST['closeLinkText']) ? sanitize_text_field($_POST['closeLinkText']) : 'X';
            $this->delay = isset($_POST['delay']) ? intval($_POST['delay']) : 900;
            $this->fadeRate = isset($_POST['faderate']) ? intval($_POST['faderate']) : 300;
            $this->dragTips = isset($_POST['dragTips']) ? "true" : "false";
            $this->dragHeader = isset($_POST['dragHeader']) ? "true" : "false";

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
        // Set an id after checking if one is in the attributes
        if (empty($attributes['id'])) {
            $tippyId = 'tippy_tip'. $tippyItem .'_'. rand(100, 9999);
        } else {
            $tippyId = $attributes['id'];

            // We no longer need nor want this in the attributes list
            unset($attributes['id']);
        }

        // Loop through $attributes and make sure they are in $this->tippyOptionNames
        // then add them to our data set
        foreach ($attributes as $attName => $attValue) {
            if (in_array($attName, $this->tippyOptionNames)) {
                $this->addAttribute($attName, $attValue, $tippyId);
            }
        }

        $this->addContent($text, $tippyId);
        
        $this->countTips++;
        
        return $tippyLink;
    }

    private function addAttribute($attributeName, $attributeValue, $contentId)
    {
        $this->tippyAttributes[$contentId][$attributeName] = $attributeValue;
    }

    private function addContent($contentText, $contentId)
    {
        // Put the attributes together
        $tooltipAttributes = '';
        
        foreach ($this->tippyAttributes[$contentId] as $attributeName => $attributeValue) {
            $tooltipAttributes .= 'data-'. $attributeName .'="'. $attributeValue .'" ';
        }

        $tooltipDiv = '<div class="tippy" '. $tooltipAttributes .'>'. do_shortcode($contentText) .'</div>';

        $this->tippyContent[$contentId] = $tooltipDiv;
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