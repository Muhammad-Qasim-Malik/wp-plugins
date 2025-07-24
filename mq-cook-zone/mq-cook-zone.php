<?php
/**
 * Plugin Name: MQ Cook Zone
 * Description: A plugin for user login, registration, and recipe management by MQ.
 * Version: 1.0
 * Author: Muhammad Qasim
 * Text Domain: mq-cook-zone
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


/**
 * Define Constrants
 */
define( 'MQCZ_VERSION', '1.0' );
define( 'MQCZ_PATH', plugin_dir_path( __FILE__ ) );
define( 'MQCZ_URL', plugin_dir_url( __FILE__ ) );


$mqcz_includes = [
    'functions.php',
    'includes/shortcodes/index.php',
    'includes/admin/index.php',
];

foreach ( $mqcz_includes as $file ) {
    require_once MQCZ_PATH . $file;
}


/**
 * Enqueue Scripts
 */
function mqcz_enqueue_assets() {
    wp_enqueue_style(
        'mqcz-style', 
        MQCZ_URL . 'assets/css/style.css', 
        [], 
        MQCZ_VERSION 
    );

    wp_enqueue_script(
        'mqcz-script', 
        MQCZ_URL . 'assets/js/script.js', 
        ['jquery'], 
        MQCZ_VERSION, 
        true 
    );

    wp_localize_script('mqcz-script', 'mqcz_like_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'mqcz_enqueue_assets');


function mqcz_add_custom_roles() {
    add_role( 'food_lover', 'Food Lover', [
        'read' => true,
    ] );

    add_role( 'chef_active', 'Chef (Active)', [
        'read'           => true,
        'edit_posts'     => true,
        'edit_published_posts' => true,
        'publish_posts'  => true,
        'upload_files'   => true,
    ] );

    add_role( 'chef_inactive', 'Chef (Inactive)', [
        'read' => true,
    ] );
}
register_activation_hook( __FILE__, 'mqcz_add_custom_roles' );

function mqcz_remove_custom_roles() {
    remove_role( 'food_lover' );
    remove_role( 'chef_active' );
    remove_role( 'chef_inactive' );
}
register_deactivation_hook( __FILE__, 'mqcz_remove_custom_roles' );

