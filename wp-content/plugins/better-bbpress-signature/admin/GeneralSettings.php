<?php

/**
 * 
 */
class GeneralSettings {

    private $settings = array();

    function __construct($fields = array()) {
        $this->settings = $fields;
        add_action('admin_init', array(&$this, 'init'));
    }

    function init() {
        add_settings_section(
                'b3p_setting_section', '<hr id="b3p-settings-page" />Better bbPress Signature Settings', array(&$this, 'b3p_setting_section'), 'bbpress'
        );
        foreach ($this->settings as $fieldname => $field) {
            $args = array();
            $args['id'] = $fieldname;
            $args['type'] = $field[1];
            add_settings_field($fieldname, $field[0], array(&$this, 'b3p_setting_field'), $field[2], $field[3], $args);
            register_setting($field[2], $fieldname);
        }
    }

    function b3p_setting_section() {
        echo '<p>Better bbPress Signature messages</p>';
    }

    /**
     * Create fields for the settings
     * @param array $args
     */
    function b3p_setting_field($args) {
        echo '<input name="' . $args["id"] . '" id="' . $args["id"] . '" type="' . $args["type"] . '" value="' . get_option($args["id"]) . '" class="regular-text code" />';
    }

}

$setting = new GeneralSettings(
        array(
    'b3p_character_limit' => array('Character limit for signature',
        'text',
        'bbpress',
        'b3p_setting_section'),
    'b3p_character_limit_error' => array('Error message for character limit',
        'text',
        'bbpress',
        'b3p_setting_section'),
    'b3p_no_update_error' => array('Error message when nothing is updated',
        'text',
        'bbpress',
        'b3p_setting_section'),
    'b3p_signature_updated' => array('Message on signature update',
        'text',
        'bbpress',
        'b3p_setting_section'),
    'b3p_server_error' => array('Error message when server failed to update / timed out',
        'text',
        'bbpress',
        'b3p_setting_section')
        ));
