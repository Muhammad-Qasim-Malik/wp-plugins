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
    BACKSTAGE_PLUGIN_PATH . 'includes/admin/menu.php',
    BACKSTAGE_PLUGIN_PATH . 'includes/admin/settings.php',
    BACKSTAGE_PLUGIN_PATH . 'includes/shortcodes/backstage_shortcode.php',
);

// Include each file in the array
foreach ( $includes as $file ) {
    if ( file_exists( $file ) ) {
        include_once( $file );
    }
}

function backstage_enqueue_styles() {
    wp_enqueue_style('backstage-style', BACKSTAGE_PLUGIN_URL . 'assets/style.css');
}
add_action('wp_enqueue_scripts', 'backstage_enqueue_styles');
?>
