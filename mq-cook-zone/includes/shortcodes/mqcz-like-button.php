<?php

function mqcz_handle_like() {
    if (isset($_POST['post_id'])) {
        $post_id = $_POST['post_id'];

        $likes = (int) get_post_meta($post_id, 'mqcz_likes', true);
        $likes++;  
        update_post_meta($post_id, 'mqcz_likes', $likes);  

        setcookie('liked_wprm_recipe_' . $post_id, '1', time() + 3600, COOKIEPATH, COOKIE_DOMAIN);

        echo json_encode(array('likes' => $likes));

        wp_die(); 
    }
}
add_action('wp_ajax_mqcz_handle_like', 'mqcz_handle_like');  
add_action('wp_ajax_nopriv_mqcz_handle_like', 'mqcz_handle_like');  

function mqcz_enqueue_like_button_scripts() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var post_id = $('#mqcz-like-btn').data('post-id');

            if (document.cookie.indexOf('liked_wprm_recipe_' + post_id) !== -1) {
                $('#mqcz-like-btn').find('i').removeClass('fa-heart-o').addClass('fa-heart').css('color', 'red');
                $('#mqcz-like-btn').css('pointer-events', 'none'); 
            }

            $('#mqcz-like-btn').on('click', function() {
                var like_button = $(this);

                like_button.css('pointer-events', 'none');  
                like_button.find('i').removeClass('fa-heart-o').addClass('fa-heart').css('color', 'red');

                document.cookie = "liked_wprm_recipe_" + post_id + "=1; path=/; max-age=31536000"; // Set cookie for 1 year

                // Send the AJAX request to increment likes
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    method: 'POST',
                    data: {
                        action: 'mqcz_handle_like',
                        post_id: post_id
                    },
                    success: function(response) {
                    }
                });
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'mqcz_enqueue_like_button_scripts');  



?>
