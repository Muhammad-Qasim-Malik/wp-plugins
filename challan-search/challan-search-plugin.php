<?php
/**
 * Plugin Name: Challan Search Plugin
 * Description: Allows admin to upload challan data and provides a public search page.
 * Version: 1.0.0
 * Author: Muhammad Qasim
 * Text Domain: challan-search-plugin
 */

/**
 * Prevent direct access to the file
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define Constants
 * @since 1.0.0
 */
define('CHALLAN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CHALLAN_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Include necessary files
 * @since 1.0.0
 */
require_once CHALLAN_PLUGIN_DIR . 'includes/classes/class_database.php';
require_once CHALLAN_PLUGIN_DIR . 'includes/admin.php';
require_once CHALLAN_PLUGIN_DIR . 'includes/menu.php';
require_once CHALLAN_PLUGIN_DIR . 'includes/activation.php';
require_once CHALLAN_PLUGIN_DIR . 'includes/helper.php';
require_once CHALLAN_PLUGIN_DIR . 'includes/shortcodes/index.php';

/**
 * Enqueue Script
 * @since 1.0.0
 */
function challan_enqueue_scripts() {
    // Enqueue JavaScript file
    wp_enqueue_script(
        'challan-search-script',
        CHALLAN_PLUGIN_URL . 'includes/js/challan-search.js',
        array('jquery'), // Ensure jQuery dependency
        filemtime(plugin_dir_path(__FILE__) . 'includes/js/challan-search.js'), 
        true 
    );
    // Enqueue CSS file
    wp_enqueue_style(
        'challan-search-style',
        CHALLAN_PLUGIN_URL . 'includes/css/challan-search.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'includes/css/challan-search.css') 
    );
}

add_action('admin_enqueue_scripts', 'challan_enqueue_scripts');

add_action('wp_enqueue_scripts', 'challan_enqueue_scripts');

/**
 * Redirect non logged in users
 * @since 1.0.0
 */

// function redirect_non_logged_in_users() {
//     if (!is_user_logged_in() && (is_page() || is_single())) {
//         if (has_shortcode(get_post()->post_content, 'challan_form') || has_shortcode(get_post()->post_content, 'challan_result')) {
//             wp_safe_redirect(home_url()); 
//             exit; 
//         }
//     }
// }

// add_action('template_redirect', 'redirect_non_logged_in_users');


/**
 * Activation and Deactivation hooks
 * @since 1.0.0
 */
$activate = new Activation();
register_activation_hook(__FILE__, [$activate, 'challan_search_activate']);
register_deactivation_hook(__FILE__, [$activate, 'challan_search_deactivate']);

?>
