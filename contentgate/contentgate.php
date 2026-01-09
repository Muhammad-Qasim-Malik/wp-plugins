<?php

/*
 * Plugin Name: ContentGate
 * Description: A plugin to manage gated content on your WordPress site.
 * Version: 1.0.0
 * Author: Muhammad Qasim
 * License: GPL2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'CONTENTGATE_VERSION', '1.0.0' );
define( 'CONTENTGATE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CONTENTGATE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CONTENTGATE_PLUGIN_FILE', __FILE__ );
define( 'CONTENTGATE_TEXT_DOMAIN', 'contentgate' );


// Include required files
$includes = [
    'admin/metabox.php',
    // 'admin/settings-page.php',
    // 'includes/contentgate-functions.php',
    // 'includes/contentgate-shortcodes.php',
];

foreach ( $includes as $file ) {
    require_once CONTENTGATE_PLUGIN_DIR . $file;
}


// Enqueue admin scripts
add_action( 'admin_enqueue_scripts', 'contentgate_enqueue_admin_scripts' );
function contentgate_enqueue_admin_scripts( $hook ) {
    if ( ! in_array( $hook, ['post.php','post-new.php'], true ) ) {
        return;
    }

    wp_enqueue_script(
        'contentgate-admin',
        CONTENTGATE_PLUGIN_URL . 'admin/assets/script.js',
        ['jquery'],
        '1.0',
        true
    );
}



add_action( 'template_redirect', 'contentgate_override_singular' );

function contentgate_override_singular() {

    // Only apply on singular posts/pages
    if ( ! is_singular() ) {
        return;
    }

    global $post;

    // Get rules from post meta
    $rules = get_post_meta( $post->ID, 'contentgate_rules', true );

    // If restriction not enabled, do nothing
    if ( empty( $rules['enabled'] ) ) {
        return;
    }

    $condition = $rules['condition'] ?? '';
    $roles     = $rules['roles'] ?? [];
    $days      = $rules['days'] ?? [];
    $message   = $rules['message'] ?? 'You do not have permission to view this content.';

    if($message === ''){
        $message = 'You do not have permission to view this content.';
    }
    $show_content = true; // Default visible

    // Check condition
    switch ( $condition ) {

        case 'logged_in':
            if ( ! is_user_logged_in() ) {
                $show_content = false;
            }
            break;

        case 'logged_out':
            if ( is_user_logged_in() ) {
                $show_content = false;
            }
            break;

        case 'user_role':
            if ( is_user_logged_in() ) {
                $user = wp_get_current_user();
                $allowed = false;
                foreach ( $roles as $role ) {
                    if ( in_array( $role, $user->roles, true ) ) {
                        $allowed = true;
                        break;
                    }
                }
                if ( ! $allowed ) {
                    $show_content = false;
                }
            } else {
                $show_content = false;
            }
            break;

        case 'day_of_week':
            $today = strtolower( date( 'l' ) );
            if ( ! in_array( $today, $days, true ) ) {
                $show_content = false;
            }
            break;

        default:
            $show_content = true;
            break;
    }

    if ( ! $show_content ) {

        // Set HTTP status
        status_header( 403 );

        // Load theme header
        get_header();

        // Output restriction message
        echo '<div style="
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
            padding: 40px 20px;
            background: #f5f5f5;
            margin: 0px auto;
        ">
            <div style="
                background: #fff;
                padding: 30px 25px;
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                text-align: center;
                font-size: 20px;
                color: #333;
                max-width: 600px;
                width: 100%;
            ">
                ' . esc_html( $message ) . '
            </div>
        </div>';

        // Load theme footer
        get_footer();

        exit;
    }


}
