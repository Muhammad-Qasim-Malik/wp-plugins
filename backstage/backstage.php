<?php
/*
Plugin Name: Backstage by MQ
Description: A plugin that lets the admin upload a background image and use a shortcode to display the post title and logo over the background.
Version: 1.0
Author: Muhammad Qasim
text-domain: backstage-by-mq
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Unauthorized access' );
}

// Define plugin constants
define('BACKSTAGE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BACKSTAGE_PLUGIN_PATH', plugin_dir_path(__FILE__));

$includes = array(
    BACKSTAGE_PLUGIN_PATH . 'includes/admin/backstage_menu.php',
    BACKSTAGE_PLUGIN_PATH . 'includes/admin/backstage_settings.php',
    BACKSTAGE_PLUGIN_PATH . 'includes/admin/backstage_upload.php',
    BACKSTAGE_PLUGIN_PATH . 'includes/shortcodes/backstage_shortcode.php',
    BACKSTAGE_PLUGIN_PATH . 'vendor/autoload.php',
);

// Include each file in the array
foreach ( $includes as $file ) {
    if ( file_exists( $file ) ) {
        include_once( $file );
    }
}

function backstage_check_dependency() {
    if ( ! class_exists( 'Elementor\Plugin' ) ) {
        add_action( 'admin_notices', 'backstage_dependency_notice' );
        return false;
    }
    return true;
}

function backstage_dependency_notice() {
    echo '<div class="error"><p><strong>' . __( 'Backstage', 'backstage-by-mq' ) . '</strong> ' . __( 'requires Elementor to be installed and activated.', 'mq-elementor-widgets' ) . '</p></div>';
}

add_action( 'plugins_loaded', function() {
    if ( backstage_check_dependency() ) {
        add_action( 'elementor/widgets/widgets_registered', function() {
            require_once( BACKSTAGE_PLUGIN_PATH . 'includes/widgets/backstage_widget.php' );
        });
    }
});



function backstage_enqueue_styles() {
    wp_enqueue_style('backstage-style', BACKSTAGE_PLUGIN_URL . 'assets/style.css');
}
add_action('wp_enqueue_scripts', 'backstage_enqueue_styles');

function backstage_admin_enqueue_styles() {
    wp_enqueue_style('backstage-admin-style', BACKSTAGE_PLUGIN_URL . 'assets/admin-style.css');
}
add_action('admin_enqueue_scripts', 'backstage_admin_enqueue_styles');
?>
