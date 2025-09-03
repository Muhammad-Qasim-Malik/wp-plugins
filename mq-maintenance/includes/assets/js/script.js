jQuery(document).ready(function($){
    $(window).on('load', function() {
        jQuery('#mq-loader').fadeOut(500); 
    });

    $('#subscribe-form-submit').click(function(e){
        e.preventDefault();
        var email = $('#subscribe-email').val();
        if(email) {
            $.post(
                mq_maintenance_script.ajaxurl,
                {
                    action : 'ajax_subscribe_handler',
                    email : email
                },
                function(response){
                    console.log(response);
                    if(response.success){
                        $('.subscribe-message').addClass('success').removeClass('error').html(response.data);
                    } else {
                        $('.subscribe-message').addClass('error').removeClass('success').html(response.data);
                    }
                }
            )
        } else {
            $('.subscribe-message').html('Please Enter a valid Email.').addClass('error');
        }
    })
})