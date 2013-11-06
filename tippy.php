<?php
/*
Plugin Name: Tippy
Plugin URI: http://croberts.me/tippy/
Description: Simple plugin to display tooltips within your WordPress blog.
Version: 6.2.1
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
    // Initialize default options
    private static $tippyGlobalOptions = array(
                    'openTip' => 'hover',
                    'fadeTip' => 'fade',
                    'tipPosition' => 'link',
                    'tipOffsetX' => 0,
                    'tipOffsetY' => 10,
                    'tipContainer' => false,
                    'linkWindow' => 'same',
                    'sticky' => false,
                    'showTitle' => true,
                    'showClose' => true,
                    'closeLinkText' => 'X',
                    'delay' => 700,
                    'fadeRate' => 200,
                    'dragTips' => true,
                    'dragHeader' => true,
                    'multitip' => false,
                    'autoshow' => false,
                    'showdelay' => 100,
                    'hidespeed' => 200,
                    'showheader' => true,
                    'calcpos' => 'parent',
                    'htmlentities' => false);

    // List all options possible for Tippy. Used to verify valid attributes
    // in the shortcode.
    private static $tippyOptionNames = array(
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
                    'closetext',
                    'class',
                    'id',
                    'name',
                    'calcpos',
                    'htmlentities',
                    'hasnested',
                    'subtip',
                    'swaponhover',
                    'alttrigger');
    
    // Various helper properties
    private static $optionsLoaded = false;
    private static $countTips = 0;

    private static $tippyContent = array();
    private static $tippyAttributes = array();
    private static $tippyObject = '';

    // Initialize everything
    public static function init()
    {
        add_action('wp_enqueue_scripts', array('Tippy', 'load_scripts'));
        add_action('wp_enqueue_scripts', array('Tippy', 'load_styles'));
        add_action('wp_head', array('Tippy', 'initialize_tippy'));
        add_shortcode('tippy', array('Tippy', 'getLink'));

        add_filter('the_content', array('Tippy', 'insert_tippy_content'), 55);

        // Admin tasks
        add_action('admin_menu', array('Tippy', 'admin_menu'));
        add_action('admin_init', array('Tippy', 'admin_init'));
        add_action('admin_action_tippy-options', array('Tippy', 'admin_validate_options'));

        add_filter('plugin_action_links', array('Tippy', 'settings_link'), 10, 2);
    }

    public static function settings_link($links, $file) { 
        if ($file == 'tippy/tippy.php') {
            $settings_link = '<a href="options-general.php?page=tippy.php">Settings</a>'; 
            array_push($links, $settings_link);
        }
        
        return $links; 
    }

    private static function loadOptions()
    {
        // Grab the Tippy array.
        $optionsArray = get_option('tippy_options', array());

        // If the array is empty, either no options have been saved or the user
        // used an older Tippy before the options array. If we find old options,
        // update the user options.
        if (empty($optionsArray) && get_option('tippy_openTip', false)) {
            self::updateOutdated();
        } else if (!empty($optionsArray)) {
            $mergedOptions = array_merge(self::$tippyGlobalOptions, $optionsArray);

            self::$tippyGlobalOptions = $mergedOptions;
        } else {
            // No options stored, rely on already set default values. Save them
            // for next time.
            self::saveOptions();
        }

        self::$optionsLoaded = true;
    }

    private static function updateOutdated()
    {
        // Load the old individual options with our predefined defaults as backup
        self::$tippyGlobalOptions['openTip'] = get_option('tippy_openTip', self::$tippyGlobalOptions['openTip']);
        self::$tippyGlobalOptions['fadeTip'] = get_option('tippy_fadeTip', self::$tippyGlobalOptions['fadeTip']);
        self::$tippyGlobalOptions['tipPosition'] = get_option('tippy_tipPosition', self::$tippyGlobalOptions['tipPosition']);
        self::$tippyGlobalOptions['tipOffsetX'] = get_option('tippy_tipOffsetX', self::$tippyGlobalOptions['tipOffsetX']);
        self::$tippyGlobalOptions['tipOffsetY'] = get_option('tippy_tipOffsetY', self::$tippyGlobalOptions['tipOffsetY']);
        self::$tippyGlobalOptions['linkWindow'] = get_option('tippy_linkWindow', self::$tippyGlobalOptions['linkWindow']);
        self::$tippyGlobalOptions['sticky'] = get_option('tippy_sticky', self::$tippyGlobalOptions['sticky']);
        self::$tippyGlobalOptions['showTitle'] = get_option('tippy_showTitle', self::$tippyGlobalOptions['showTitle']);
        self::$tippyGlobalOptions['showClose'] = get_option('tippy_showClose', self::$tippyGlobalOptions['showClose']);
        self::$tippyGlobalOptions['closeLinkText'] = get_option('tippy_closeLinkText', self::$tippyGlobalOptions['closeLinkText']);
        self::$tippyGlobalOptions['delay'] = get_option('tippy_delay', self::$tippyGlobalOptions['delay']);
        self::$tippyGlobalOptions['fadeRate'] = get_option('tippy_faderate', self::$tippyGlobalOptions['fadeRate']);

        // Save options to the new array and delete the old options
        if (self::saveOptions()) {
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

    private static function saveOptions()
    {
        // Save the options array
        return update_option('tippy_options', self::$tippyGlobalOptions);
    }

    public static function getOption($optionName)
    {
        // Check to see if we've already tried to load saved options. If not, load them.
        if (!self::$optionsLoaded) {
            self::loadOptions();
        }

        if (isset(self::$tippyGlobalOptions[$optionName])) {
            return self::$tippyGlobalOptions[$optionName];
        } else {
            return false;
        }
    }

    public static function register_scripts()
    {
        wp_register_script('Tippy', plugins_url() .'/tippy/jquery.tippy.js', array('jquery'), '6.0.0');
    }

    public static function load_scripts()
    {
        // Load jQuery, if not already present
        wp_enqueue_script('jquery');
        
        if (self::getOption('dragTips')) {
            wp_enqueue_script('jquery-ui-draggable');
        }
        
        // Load the Tippy script
        self::register_scripts();
        wp_enqueue_script('Tippy');
    }

    public static function register_styles()
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

    public static function load_styles()
    {
        self::register_styles();
        wp_enqueue_style('Tippy');
    }

    public static function initialize_tippy()
    {
        if (!self::getOption('tipContainer') || self::getOption('tipContainer') == "") {
            $setContainer = '';
        } else {
            $setContainer = self::getOption('tipContainer');
        }
        
        // Prepare the js object with our options
        $setOptions = 'position: "'. self::getOption("tipPosition") .'", ';
        $setOptions .= 'offsetx: '. self::getOption("tipOffsetX") .', ';
        $setOptions .= 'offsety: '. self::getOption("tipOffsetY") .', ';
        $setOptions .= 'closetext: "'. self::getOption("closeLinkText") .'", ';
        $setOptions .= 'hidedelay: '. self::getOption("delay") .', ';
        $setOptions .= 'showdelay: '. self::getOption("showdelay") .', ';
        $setOptions .= 'calcpos: "'. self::getOption("calcpos") .'"';

        if (self::getOption('fadeTip') == "fade") {
            $setOptions .= ', showspeed: '. self::getOption('fadeRate') .'';
            $setOptions .= ', hidespeed: '. self::getOption('hidespeed') .'';
        } else {
            $setOptions .= ', showspeed: 0';
            $setOptions .= ', hidespeed: 0';
        }


        if (!empty($setContainer)) {
            $setOptions .= ', container: "'. $setContainer .'"';
        }

        if (self::getOption('sticky') === true || self::getOption('sticky') == "true") {
            $setOptions .= ', autoclose: false';
        }

        if (self::getOption('linkWindow') == "new") {
            $setOptions .= ', target: "_blank"';
        }
        
        if (self::getOption('showTitle')) {
            $setOptions .= ', showtitle: true';
        } else {
            $setOptions .= ', showtitle: false';
        }
        
        if (self::getOption('openTip') == "hover") {
            $setOptions .= ', hoverpopup: true';
        } else {
            $setOptions .= ', hoverpopup: false';
        }
        
        if (self::getOption('dragTips')) {
            $setOptions .= ', draggable: true';
        } else {
            $setOptions .= ', draggable: false';
        }
        
        if (self::getOption('dragHeader')) {
            $setOptions .= ', dragheader: true';
        } else {
            $setOptions .= ', dragheader: false';
        }
        
        if (self::getOption('multitip')) {
            $setOptions .= ', multitip: true';
        } else {
            $setOptions .= ', multitip: false';
        }
        
        if (self::getOption('autoshow')) {
            $setOptions .= ', autoshow: true';
        } else {
            $setOptions .= ', autoshow: false';
        }
        
        
        if (self::getOption('showheader')) {
            $setOptions .= ', showheader: true';
        } else {
            $setOptions .= ', showheader: false';
        }
        
        if (self::getOption('showClose')) {
            $setOptions .= ', showclose: true';
        } else {
            $setOptions .= ', showclose: false';
        }

        if (self::getOption('htmlentities')) {
            $setOptions .= ', htmlentities: true';
        } else {
            $setOptions .= ', htmlentities: false';
        }

        echo '
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery(\'.tippy\').tippy({ '. $setOptions .' });
                });
            </script>
        ';
    }

    public static function admin_menu()
    {
        require_once(plugin_dir_path(__FILE__) .'/tippy_admin.php');

        $page = add_options_page('Tippy Plugin Options', 'Tippy', 'manage_options', basename(__FILE__), 'tippy_options_subpanel');

        add_action('admin_print_styles-' . $page, array('Tippy', 'admin_load_styles'));
    }

    public static function admin_init()
    {
        wp_register_style('TippyAdmin', plugins_url() .'/tippy/tippy_admin.css');
        self::register_styles();
        self::register_scripts();
    }

    public static function admin_load_styles()
    {
        wp_enqueue_style('TippyAdmin');
        self::load_styles();
        self::load_scripts();
    }

    public static function admin_validate_options()
    {
        $tippy_validated = "0";

        // Retrieve and save settings
        if (isset($_POST['info_update']) && is_admin() && wp_verify_nonce($_POST['tippy_verify'], 'tippy-options')) {
            self::$tippyGlobalOptions['openTip'] = sanitize_text_field($_POST['openTip']);
            self::$tippyGlobalOptions['fadeTip'] = sanitize_text_field($_POST['fadeTip']);
            self::$tippyGlobalOptions['tipPosition'] = sanitize_text_field($_POST['tipPosition']);
            self::$tippyGlobalOptions['tipOffsetX'] = intval($_POST['tipOffsetX']);
            self::$tippyGlobalOptions['tipOffsetY'] = intval($_POST['tipOffsetY']);
            self::$tippyGlobalOptions['tipContainer'] = isset($_POST['tipContainer']) ? sanitize_text_field($_POST['tipContainer']) : false;
            self::$tippyGlobalOptions['linkWindow'] = sanitize_text_field($_POST['linkWindow']);
            self::$tippyGlobalOptions['sticky'] = ($_POST['sticky'] == "true") ? true : false;
            self::$tippyGlobalOptions['showTitle'] = isset($_POST['showTitle']) ? true : false;
            self::$tippyGlobalOptions['showClose'] = isset($_POST['showClose']) ? true : false;
            self::$tippyGlobalOptions['htmlentities'] = isset($_POST['htmlentities']) ? false : true;
            self::$tippyGlobalOptions['closeLinkText'] = isset($_POST['closeLinkText']) ? sanitize_text_field($_POST['closeLinkText']) : 'X';
            self::$tippyGlobalOptions['delay'] = isset($_POST['delay']) ? intval($_POST['delay']) : 900;
            self::$tippyGlobalOptions['fadeRate'] = isset($_POST['faderate']) ? intval($_POST['faderate']) : 300;
            self::$tippyGlobalOptions['dragTips'] = isset($_POST['dragTips']) ? true : false;
            self::$tippyGlobalOptions['dragHeader'] = isset($_POST['dragHeader']) ? true : false;
            self::$tippyGlobalOptions['multitip'] = isset($_POST['multitip']) ? true : false;
            self::$tippyGlobalOptions['autoshow'] = isset($_POST['autoshow']) ? true : false;
            self::$tippyGlobalOptions['showdelay'] = isset($_POST['showdelay']) ? intval($_POST['showdelay']) : 100;
            self::$tippyGlobalOptions['hidespeed'] = isset($_POST['hidespeed']) ? intval($_POST['hidespeed']) : 300;
            self::$tippyGlobalOptions['showheader'] = isset($_POST['showheader']) ? true : false;
            self::$tippyGlobalOptions['calcpos'] = sanitize_text_field($_POST['calcpos']);

            self::saveOptions();

            $tippy_validated = "1";
        } else if (isset($_POST['info_update'])) {
            $tippy_validated = "0";
        }

        update_option('tippy_options_updated', true);

        wp_redirect(admin_url('options-general.php?page=tippy.php') ."&tippy_updated=$tippy_validated");
    }

    // Take the attributes and generate the Tippy link and data container
    public static function getLink($attributes, $text = '')
    {
        // Set an id after checking if one is in the attributes
        if (empty($attributes['id'])) {
            $tippyId = 'tippy_tip'. self::$countTips .'_'. rand(100, 9999);
        } else {
            $tippyId = $attributes['id'];

            // We no longer need nor want this in the attributes list
            unset($attributes['id']);
        }

        // Map old Tippy attributes to their new names. Only a few changes.
        if (isset($attributes['header'])) {
            if ($attributes['header'] == "on") {
                $attributes['showheader'] = true;
            } else {
                $attributes['showheader'] = false;
            }

            unset($attributes['header']);
        }

        if (isset($attributes['headertext'])) {
            $attributes['headertitle'] = $attributes['headertext'];
            unset($attributes['headertext']);
        }
        
        // See if text is set in the attributes
        if (isset($attributes['text'])) {
            $text = $attributes['text'];
            unset($attributes['text']);
        }

        // Loop through $attributes and make sure they are in self::tippyOptionNames
        // then add them to our data set
        foreach ($attributes as $attName => $attValue) {
            if (in_array($attName, self::$tippyOptionNames)) {
                self::addAttribute($attName, $attValue, $tippyId);
            }
        }

        // Set the anchor
        self::addAttribute('anchor', '#'. $tippyId .'_anchor', $tippyId);

        // Create the div with the text in place
        if (!in_the_loop() || (isset($attributes['subtip']) && $attributes['subtip'] == true)) {
            $storeContent = false;
        } else {
            $storeContent = true;
        }

        $tooltipContent = self::addContent($text, $tippyId, $storeContent);
        
        self::$countTips++;
        
        $returnTip = '<a id="'. $tippyId .'_anchor"></a>';
        
        if (!$storeContent) {
            $returnTip .= ' '. $tooltipContent;
        }
        
        return $returnTip;
    }

    private static function addAttribute($attributeName, $attributeValue, $contentId)
    {
        self::$tippyAttributes[$contentId][$attributeName] = $attributeValue;
    }

    private static function addContent($contentText, $contentId, $storeContent = true)
    {
        // Put the attributes together
        $tooltipAttributes = '';
        
        // Balance tags
        $contentText = force_balance_tags($contentText);

        // Check for nested tooltips
        $contentText = self::getNested($contentText, $contentId);

        // Process shortcodes
        $contentText = do_shortcode($contentText);

        // See if we are converting to htmlentities
        if ((isset(self::$tippyAttributes[$contentId]['htmlentities']) && self::$tippyAttributes[$contentId]['htmlentities'] == "true") || (!isset(self::$tippyAttributes[$contentId]['htmlentities']) && self::getOption('htmlentities'))) {
            $contentText = htmlentities($contentText);
        }

        // Set up attributes
        foreach (self::$tippyAttributes[$contentId] as $attributeName => $attributeValue) {
            $tooltipAttributes .= 'data-'. $attributeName .'="'. $attributeValue .'" ';
        }

        $tooltipDiv = '<div class="tippy" '. $tooltipAttributes .'>'. $contentText .'</div>';

        if ($storeContent) {
            self::$tippyContent[$contentId] = $tooltipDiv;
        }
        
        return $tooltipDiv;
    }

    // Looks inside a tooltip for any nested tooltips. Because of the tag matching,
    // the ShortCode API is deficient and we need our own approach.
    private static function getNested($contentText, $contentId)
    {
        // Look for subtippy matches, including those with numeric suffixes.
        preg_match_all('/\[subtippy([0-9]*)?([^\]]+)?\](.*?)(?!\[subtippy)\[\/subtippy\1?\]/s', $contentText, $matchNested);

        if (!empty($matchNested[0])) {
            for ($i = 0 ; $i < sizeof($matchNested[0]) ; $i++) {
                // Flag that we have a nested item
                self::addAttribute('hasnested', true, $contentId);

                $subTag = $matchNested[0][$i];
                $subAttributes = shortcode_parse_atts(trim($matchNested[2][$i]));
                
                // Flag this so other parts of the script know we're dealing with a nested tooltip
                $subAttributes['subtip'] = true;
                $subContent = trim($matchNested[3][$i]);

                $replaceText = self::getLink($subAttributes, $subContent);
                $contentText = str_replace($subTag, $replaceText, $contentText);
            }
        }

        return $contentText;
    }

    public static function insert_tippy_content($content)
    {
        $tippyContent = '';

        if (!empty(self::$tippyContent)) {
            foreach (self::$tippyContent as $contentId => $contentDiv) {
                $tippyContent .= $contentDiv ."\r\n";
            }
        }

        // Since we've used the content, clear it out.
        self::$tippyContent = array();

        return $content . $tippyContent;
    }
}

Tippy::init();

/*
 * The following are deprecated or helper functions
 */

if (! function_exists('tippy_formatLink') ) {
    function tippy_formatLink($tippyShowHeader, $tippyTitle, $tippyHref, $tippyText, $tippyCustomClass, $tippyItem, $tippyWidth = false, $tippyHeight = false)
    {
        return Tippy::getLink(array('header' => $tippyShowHeader, 'title' => $tippyTitle, 'href' => $tippyHref, 'text' => $tippyText, 'class' => $tippyCustomClass, 'item' => $tippyItem, 'width' => $tippyWidth, 'height' => $tippyHeight));
    }
}

if (! function_exists('tippy_getLink')) {
    function tippy_getLink($tippyArray)
    {
        return Tippy::getLink($tippyArray);
    }
}

if (!function_exists('tippy_format_title')) {
    function tippy_format_title($tippy_title)
    {
        return $tippy_title;
    }
}

if (!function_exists('tippy_format_text')) {
    function tippy_format_text($tippy_text)
    {
        return $tippy_text;
    }
}

?>