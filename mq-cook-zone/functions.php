<?php
function mqcz_block_admin_for_custom_roles() {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) && is_user_logged_in() ) {
        $user = wp_get_current_user();

        $blocked_roles = [ 'food_lover', 'chef_inactive', 'chef_active', 'subscriber' ];

        if ( array_intersect( $user->roles, $blocked_roles ) ) {
            wp_redirect( site_url( '/dashboard' ) ); 
            exit;
        }
    }
}
add_action( 'admin_init', 'mqcz_block_admin_for_custom_roles' );

function mqcz_track_post_views() {
    if (is_singular('special_occasions')) {  
        global $post;  
        $post_id = $post->ID;

        if (!isset($_COOKIE['viewed_special_occasions_' . $post_id])) {
            $views = (int) get_post_meta($post_id, 'mqcz_views', true);
            $views++;

            update_post_meta($post_id, 'mqcz_views', $views);

            setcookie('viewed_special_occasions_' . $post_id, '1', time() + 3600, COOKIEPATH, COOKIE_DOMAIN);
        }
    }
}
add_action('wp', 'mqcz_track_post_views');

function mqcz_handle_delete_special_occasions() {
    if ( isset($_GET['action']) && $_GET['action'] === 'delete_special_occasions' && isset($_GET['post']) ) {
        $post_id = $_GET['post'];

        if ( isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_special_occasions_' . $post_id) ) {
            $post = get_post($post_id);
            if ($post && $post->post_type === 'special_occasions' && current_user_can('delete_post', $post_id)) {
                wp_delete_post($post_id, true); 
                wp_redirect(site_url('/dashboard')); 
                exit;
            }
        }
    }
}
add_action('admin_init', 'mqcz_handle_delete_special_occasions');


function mqcz_track_recipe_views() {
    if (is_singular('wprm_recipe')) {  
        global $post;  
        $post_id = $post->ID;

        if (!isset($_COOKIE['viewed_wprm_recipe_' . $post_id])) {
            $views = (int) get_post_meta($post_id, 'mqcz_views', true);
            $views++;  // Increment views

            update_post_meta($post_id, 'mqcz_views', $views);

            setcookie('viewed_wprm_recipe_' . $post_id, '1', time() + 3600, COOKIEPATH, COOKIE_DOMAIN);
        }
    }
}
add_action('wp', 'mqcz_track_recipe_views');

function enable_comments_for_custom_post_type($open, $post_id) {
    $post_type = get_post_type($post_id);
    if ($post_type == 'wprm_recipe') {  
        return true;  
    }
    return $open;
}
add_filter('comments_open', 'enable_comments_for_custom_post_type', 10, 2);
