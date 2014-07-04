jQuery(document).ready(function($) {
    $('#b3p_show_signature').click(function() {
        if ($(this).data('status') == 'hidden') {
            $(this).data('status', 'display');
            $('#b3p_forum_signature').show('slow');
        } else if ($(this).data('status') == 'display') {
            $(this).data('status', 'hidden');
            $('#b3p_forum_signature').hide('slow');
        }
    });
    $('#add_signature').click(function() {
        jQuery.post(formAjax.ajaxurl, {action: 'add_b3p_signature', signature_text: $('#b3p_signature').val()}, function(response) {
            $('#bbps_message').html(response);
        });
    })
});