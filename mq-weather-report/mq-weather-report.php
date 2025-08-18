<?php
/**
 * Plugin Name: MQ Weather Report
 * Description: Simple weather report plugin with shortcode [mq_weather].
 * Version: 1.0.0
 * Author: Muhammad Qasim
 * Text Domain: mq-weather-report
 */

if (!defined('ABSPATH')) exit;

define( 'MQWR_FILE', __FILE__ );
define( 'MQWR_DIR', plugin_dir_path( __FILE__ ) );
define( 'MQWR_URL', plugin_dir_url( __FILE__ ) );

// Enqueue assets
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('mqwr-style', MQWR_URL . 'assets/style.css');
    wp_enqueue_script('mqwr-script', MQWR_URL . 'assets/script.js', ['jquery'], null, true);
    wp_localize_script('mqwr-script', 'MQWeather', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('mqwr_nonce'),
    ]);
});

/**
 * Autoload includes
 */
$mqwr_includes = [
    'includes/admin.php',
    'includes/shortcode.php',
    'includes/helper.php',
];

foreach ( $mqwr_includes as $file ) {
    $path = MQWR_DIR . $file;
    if ( file_exists( $path ) ) {
        require_once $path;
    }
}

