jQuery(document).ready(function ($) {
    $('.mqcz-tabs button').on('click', function () {
        $('.mqcz-tabs button').removeClass('active');
        $('.mqcz-tab-content').hide();

        $(this).addClass('active');
        const tabId = $(this).data('tab');
        $('#' + tabId).show();
    });


    $('#mqcz-like-btn').on('click', function() {
        var post_id = $(this).data('post-id');
        $.ajax({
            url: mqcz_like_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mqcz_handle_like',
                post_id: post_id
            },
            success: function(response) {
                $('#mqcz-like-count').text(response.likes);
                $('#mqcz-like-btn').attr('disabled', true);
                $('#mqcz-like-btn').addClass('liked');
                $('#mqcz-like-btn i').removeClass('fa-heart-o').addClass('fa-heart').css('color', 'red'); 
            }
        });
    });
    $('#add-new-recipe-btn').on('click', function() {
        $('.recipe-grid-content').fadeToggle();
        var form = $('#recipe-submission-form');
         $('h3').filter(function() {
            return $(this).text() === "Your Details";  
        }).fadeToggle();
        form.fadeToggle(); 
        if ($('#recipe-submission-form').is(':visible')) {
            $('#add-new-recipe-btn').text('Close');
        } else {
            $('#add-new-recipe-btn').text('+ Add New');
        }
    });


    $('#mqcz_role').on('change', function() {
        var selectedRole = $(this).val(); 
        mqczToggleDoc(selectedRole); 
    });
    function mqczToggleDoc(role) {
        if (role === 'chef') {
            $('#mqcz-doc-upload').show();  
        } else {
            $('#mqcz-doc-upload').hide();  
        }
    }

    $('.password-field').each(function() {
        var $field = $(this);
        var $input = $field.find('input[type="password"]');
        var $eyeIcon = $field.find('.eye-icon');

        $eyeIcon.on('click', function() {
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');  // Show the password
                $eyeIcon.html('<i class="fas fa-eye-slash" style="cursor: pointer; position: absolute; right: 10px; top: 8px; transform: translateY(50%);"></i>');
            } else {
                $input.attr('type', 'password');  // Hide the password
                $eyeIcon.html('<i class="fas fa-eye" style="cursor: pointer; position: absolute; right: 10px; top: 8px; transform: translateY(50%);"></i>');
            }
        });
    });

    $('#mqcz-show-level').on('mouseenter', function() {
        $('#mqcz-level-info').fadeIn();  
    });

    $('#mqcz-show-level, #mqcz-level-info').on('mouseleave', function() {
        $('#mqcz-level-info').fadeOut();
    });

});
