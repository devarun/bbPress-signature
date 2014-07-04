<?php

/*
  Plugin Name: Better bbPress Signature
  Plugin URI: http://www.sparxitsolutions.com/
  Description: This plugin will add option for adding signature in the bbPress forum. The signature form will appear under the text area in topic form and reply form.
  Version: 1.2.0
  Author: Arun Singh
  Author Email: arun@sparxtechnologies.com
 */


/*
 * Define constants for the plugin
 */

define("B3P_VERSION", "1.2.0");
define("B3P_PATH", dirname(__FILE__));
define("B3P_URL", plugins_url("", __FILE__));

function b3p_signature_scripts() {
    wp_enqueue_script('jquery');
    wp_register_script('b3p_signature', B3P_URL . '/js/bbpress-signature.js', array('jquery'), '1.0', TRUE);
    wp_enqueue_script('b3p_signature');
    wp_localize_script('b3p_signature', 'formAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}

function b3p_signature_styles() {
    wp_register_style('b3p_signature_css', B3P_URL . '/css/bbpress-signature.css', '', '1.0', false);
    wp_enqueue_style('b3p_signature_css');
}

if (!is_admin()) {
    add_action('wp_enqueue_scripts', 'b3p_signature_scripts');
    add_action('wp_enqueue_scripts', 'b3p_signature_styles');
} // end if/else
add_action("wp_ajax_add_b3p_signature", "add_b3p_signature");
add_action("wp_ajax_nopriv_add_b3p_signature", "add_b3p_signature");

/*
 * Function to add store members signature in database
 * uses wordpress add_user_meta function for db interaction
 */

function add_b3p_signature() {
    global $wpdb;
    global $current_user;
    $signature_text = $_REQUEST['signature_text'];
    if (strlen($signature_text) > get_option('b3p_character_limit')) {
        $response = __(get_option('b3p_character_limit_error'), 'b3p-signatures');
    } else {
        $prev_signature = get_user_meta($current_user->ID, 'b3p_signature', true);
        if ($signature_text != $prev_signature)
            $added = update_user_meta($current_user->ID, 'b3p_signature', $signature_text, $prev_signature);
        else
            die(__(get_option('b3p_no_update_error'), 'b3p-signatures'));

        if ($added) {
            $response = __(get_option('b3p_signature_updated'), 'b3p-signatures');
        } else {
            $response = __(get_option('b3p_server_error'), 'b3p-signatures');
        }
    }
    die($response);
}

/*
 * Function to add signature form in the front end
 */

function b3p_add_signature_form() {
    $form = '<div class="bbPress-signature"><p class="fl"><a href="javascript:;" class="button" id="b3p_show_signature"><span>'.__('Add/Edit Signature', 'b3p-signatures').'</span></a></p>';
    $form .= '<div style="display: none;" id="b3p_forum_signature">';
    $form .= '<textarea id="b3p_signature" >' . b3p_signature() . '</textarea>';
    $form .= '<p class="fr"><a href="javascript:;" class="button" id="add_signature"><span>'.__('UPDATE', 'b3p-signatures').'</span></a></p>';
    $form .= '<p class="fl" id="bbps_message"></p>';
    $form .= '</div></div>';
    echo $form;
}

/*
 * Function to retrieve current users signature from database
 * uses wordpress get_user_meta function for db interaction
 */

function b3p_signature() {
    global $wpdb;
    global $current_user;
    $current_user_signature = get_user_meta($current_user->ID, 'b3p_signature', true);
    return stripslashes($current_user_signature);
}

/*
 * Function to retrieve reply authors signature from database
 * uses wordpress get_user_meta function for db interaction
 */

function b3p_get_signature() {
    $reply_author = get_the_author_meta('ID');
    global $wpdb;
    $user_signature = get_user_meta($reply_author, 'b3p_signature', true);
    return stripslashes($user_signature);
}

/*
 * Function to embed authors signature in the reply content
 */

function embed_b3p_signature($content = '') {
    $content .= '<hr />' . b3p_get_signature();
    return $content;
}

add_filter('bbp_get_reply_content', 'embed_b3p_signature');
add_filter('bbp_get_topic_content', 'embed_b3p_signature');
add_action('bbp_theme_before_reply_form_tags', 'b3p_add_signature_form');
add_action('bbp_theme_before_topic_form_tags', 'b3p_add_signature_form');

if(is_admin()){
    require_once B3P_PATH.'/admin/GeneralSettings.php';
}