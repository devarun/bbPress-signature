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
global $b3p_message;
$b3p_message = array(
    '101' => __('<span class="b3p-success">' . get_option('b3p_signature_updated') . '</span>', 'b3p-signatures'),
    '102' => __('<span class="b3p-error">' . get_option('b3p_no_update_error') . '</span>', 'b3p-signatures'),
    '103' => __('<span class="b3p-error">' . get_option('b3p_server_error') . '</span>', 'b3p-signatures'),
    '104' =>  __('<span class="b3p-error">' . get_option('b3p_character_limit_error') . '</span>', 'b3p-signatures')
);

function b3p_signature_scripts() {
    wp_enqueue_script('jquery');
    wp_register_script('b3p_signature', B3P_URL . '/js/bbpress-signature.js', array('jquery'), '1.0', TRUE);
    wp_enqueue_script('b3p_signature');
    wp_localize_script('b3p_signature', 'b3p_data', array('ajaxurl' => admin_url('admin-ajax.php'), 'add_button_text' => get_option('b3p_add_button'), 'hide_button_text' => get_option('b3p_hide_button')));
}

function b3p_signature_styles() {
    wp_register_style('b3p_signature_css', B3P_URL . '/css/bbpress-signature.css', '', '1.0', false);
    wp_enqueue_style('b3p_signature_css');
}

if (!is_admin()) {
    if(!get_option('b3p_disable_javascript')){
    add_action('wp_enqueue_scripts', 'b3p_signature_scripts');
    }
    add_action('wp_enqueue_scripts', 'b3p_signature_styles');
} // end if/else
add_action("wp_ajax_add_b3p_signature", "add_b3p_signature");
add_action("wp_ajax_nopriv_add_b3p_signature", "add_b3p_signature");

/*
 * Function to add store members signature in database
 * uses wordpress add_user_meta function for db interaction
 */

function add_b3p_signature() {
    global $b3p_msg_code;
    global $b3p_message;
    global $current_user;
    if (isset($_REQUEST['doing_ajax']) && $_REQUEST['doing_ajax'])
        $signature_text = $_REQUEST['signature_text'];
    else if (isset($_POST['b3p_signature']) && $_POST['b3p_signature'] && isset ($_POST['b3p_update_signature']) && $_POST['b3p_update_signature'] == 'yes')
        $signature_text = $_POST['b3p_signature'];
    else
        return;
    if (strlen($signature_text) > get_option('b3p_character_limit')) {
        $b3p_msg_code = '104';
    } else {
        $prev_signature = get_user_meta($current_user->ID, 'b3p_signature', true);
        $added = update_user_meta($current_user->ID, 'b3p_signature', $signature_text, $prev_signature);

        if ($added) {
            $b3p_msg_code = '101';
        } else if ($signature_text == $prev_signature) {
            $b3p_msg_code = '102';
        } else {
            $b3p_msg_code = '103';
        }
    }

    add_filter('bbp_new_reply_redirect_to', "b3p_messages");
    if (isset($_REQUEST['doing_ajax']) && $_REQUEST['doing_ajax']) {
        die($b3p_message[$b3p_msg_code]);
    }
}

function b3p_messages($reply_url, $redirect_to, $reply_id) {
    global $b3p_msg_code;
    $reply_url = add_query_arg(array("b3p_message_id" => $b3p_msg_code), $reply_url);

    return $reply_url;
}

/**
 * Add action to update signature on reply or topic form submit
 */
add_action('bbp_new_reply_post_extras', 'add_b3p_signature');
add_action('bbp_new_topic_post_extras', 'add_b3p_signature');

/*
 * Function to add signature form in the front end
 */

function b3p_add_signature_form() {
    global $b3p_message;
    $msg = isset($_GET['b3p_message_id']) ? $b3p_message[$_GET['b3p_message_id']] : '';
    $form = '<div class="bbPress-signature">';
    if(get_option('b3p_disable_javascript')){
        $form .= '<p><strong>' . __(get_option('b3p_add_button'), 'b3p-signatures') . '</strong></p><p><input name="b3p_update_signature" id="b3p_update_signature" type="checkbox" value="yes" class="code" />'.'<label for="bbp_topic_subscription">Update Signature</label></p>';
    }else{
    $form .= '<p class="fl"><button type="button" class="button" data-status="hidden" id="b3p_show_signature"><span>' . __(get_option('b3p_add_button'), 'b3p-signatures') . '</span></button></p>';
    }
    $form .= '<div id="b3p_forum_signature">';
    $form .= '<textarea name="b3p_signature" id="b3p_signature" rows="5" >' . b3p_signature() . '</textarea>';
    $form .= '<p class="fr"><button type="submit" onclick="return false;" style="display:none" class="button" id="add_signature"><span>' . __(get_option('b3p_update_button'), 'b3p-signatures') . '</span></button></p>';
    $form .= '<p class="fl" id="bbps_message">' . $msg . '</p>';
    $form .= '</div></div>';
    echo $form;
}

/*
 * Function to retrieve current users signature from database
 * uses wordpress get_user_meta function for db interaction
 */

function b3p_signature() {
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

if (is_admin()) {
    require_once B3P_PATH . '/admin/GeneralSettings.php';
}