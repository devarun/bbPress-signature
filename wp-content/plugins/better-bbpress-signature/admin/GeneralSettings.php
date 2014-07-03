<?php

/**
 * 
 */
class GeneralSettings {

    function __construct() {
        add_action('admin_init', array(&$this, 'init'));
    }

    function init() {
        add_settings_section(
                'b3p_setting_section', '<hr / id="b3p-settings-page">Better bbPress Signature Settings', array(&$this, 'b3p_setting_section'), 'bbpress'
        );
    }

    function b3p_setting_section() {
        echo '<p>Better bbPress Signature messages</p>';
    }

    function b3p_add_setting($id, $title, $page, $type, $section, $args) {
        require_once(ABSPATH . '/wp-admin/includes/plugin.php');
        require_once(ABSPATH . WPINC . '/pluggable.php');
        global $param;
        $param = $type;

        add_settings_field($id, $title, array(&$this, 'b3p_setting_field'), $page, $section, $args);

        register_setting($page, $id);
    }

    function b3p_setting_field() {
        global $param;

        echo $param . '<input name="b3p_character_limit" id="b3p_character_limit" type="text" value="' . get_option('b3p_character_limit') . '" class="code" />';
    }

}

$setting = new GeneralSettings();
$setting->b3p_add_setting('b3p_character_limit',
		'Character limit for signature',
		'text',
		'bbpress',
		'b3p_setting_section', array());
/*function b3p_settings_api_init() {
 	
 	
 	add_settings_field(
		'b3p_character_limit',
		'Character limit for signature',
		'b3p_setting',
		'bbpress',
		'b3p_setting_section'
	);
 	
 	register_setting( 'bbpress', 'b3p_character_limit' );
 } // eg_settings_api_init()
 
 add_action( 'admin_init', 'b3p_settings_api_init' );
 

 
 function b3p_setting_section() {
 	echo '<p>Better bbPress Signature messages</p>';
 }
 
 
 function b3p_setting() {
 	echo '<input name="b3p_character_limit" id="b3p_character_limit" type="text" value="'.get_option( 'b3p_character_limit' ).'" class="code" />';
 } */