jQuery(document).ready(function($) {
    $('#b3p_forum_signature').hide();
    $('#add_signature').show();
    $('#b3p_show_signature').click(function() {
        if ($(this).data('status') == 'hidden') {
            $(this).data('status', 'display').children('span').html(b3p_data.hide_button_text);
            $('#b3p_forum_signature').show('slow');
        } else if ($(this).data('status') == 'display') {
            $(this).data('status', 'hidden').children('span').html(b3p_data.add_button_text);
            $('#b3p_forum_signature').hide('slow');
        }
    });
    $('#add_signature').click(function() {
        jQuery.post(b3p_data.ajaxurl, {action: 'add_b3p_signature', signature_text: $('#b3p_signature').val(), doing_ajax : true}, function(response) {
            $('#bbps_message').html(response);
        });
    })
});