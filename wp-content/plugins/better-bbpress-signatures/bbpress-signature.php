<?php
/*
  Plugin Name: Better bbPress Signature
  Plugin URI: http://codeaholic.wordpress.com/
  Description: This plugin will add option for adding signature in the bbPress forum. The signature form will appear under the text area in topic form and reply form.
  Version: 0.1
  Author: Arun Singh
  Author Email: arun@sparxtechnologies.com
 */
function b3p_signature_scripts(){
	wp_enqueue_script('jquery');
    wp_register_script('b3p_signature', WP_PLUGIN_URL . '/better-bbpress-signatures/js/bbpress-signature.js', $deps, $ver, TRUE);
    wp_enqueue_script('b3p_signature');
    wp_localize_script('b3p_signature', 'formAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
function b3p_signature_styles(){
	wp_register_style('b3p_signature_css', WP_PLUGIN_URL . '/better-bbpress-signatures/css/bbpress-signature.css', $deps, $ver, false);
    wp_enqueue_style('b3p_signature_css');
}

if (!is_admin()) {
	add_action( 'wp_enqueue_scripts', 'b3p_signature_scripts' );
	add_action( 'wp_enqueue_scripts', 'b3p_signature_styles' );
} // end if/else
add_action("wp_ajax_add_b3p_signature", "add_b3p_signature");
add_action("wp_ajax_nopriv_add_b3p_signature", "add_b3p_signature");

function add_b3p_signature() {
    global $wpdb;
    global $current_user;
    $signature_text = $_REQUEST['signature_text'];
    if (strlen($signature_text) > 250) {
        $response = "Sorry the signature is too long. Max limit 250 characters.";
    } else {
        if (b3p_signature()) {
            $wpdb->update(
                    $wpdb->usermeta, array('meta_value' => $signature_text), array('user_id' => $current_user->ID, 'meta_key' => 'b3p_signature'), array('%s'), array('%d', '%s')
            );
            $response = "Your signature has been updated successfully! Changes will take effect on page refresh.";
        } else {
            $result = $wpdb->insert(
                            $wpdb->usermeta, array(
                        'user_id' => $current_user->ID,
                        'meta_key' => 'b3p_signature',
                        'meta_value' => $signature_text
                            ), array(
                        '%d',
                        '%s',
                        '%s',
                            )
            );
            $response = "Your signature has been added successfully! Changes will take effect on page refresh.";
        }
    }
    die($response);
}

function b3p_add_signature_form() {
    $form = '<div class="bbPress-signature"><p class="fl"><a href="javascript:;" class="button" id="b3p_show_signature"><span>Add/Edit Signature</span></a></p>';
    $form .= '<div style="display: none;" id="b3p_forum_signature">';
    $form .= '<textarea id="b3p_signature" >' . b3p_signature() . '</textarea>';
    $form .= '<p class="fr"><a href="javascript:;" class="button" id="add_signature"><span>Update</span></a></p>';
    $form .= '<p class="fl" id="bbps_message"></p>';
    $form .= '</div></div>';
    echo $form;
}

function b3p_signature() {
    global $wpdb;
    global $current_user;
    $user_signature = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM $wpdb->usermeta WHERE meta_key='b3p_signature' AND user_id= '$current_user->ID';"));
    return stripslashes($user_signature);
}

function b3p_get_signature(){
    $reply_author = get_the_author_meta( 'ID' );
    global $wpdb;
    $user_signature = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM $wpdb->usermeta WHERE meta_key='b3p_signature' AND user_id= '$reply_author';"));
    return stripslashes($user_signature);
}


function embed_b3p_signature($content = '') {
        $content .= '<hr />'.b3p_get_signature();
    return $content;
}

add_filter('bbp_get_reply_content', 'embed_b3p_signature');
add_filter('bbp_get_topic_content', 'embed_b3p_signature');
add_action('bbp_theme_before_reply_form_tags','b3p_add_signature_form');
add_action('bbp_theme_before_topic_form_tags','b3p_add_signature_form');
?>