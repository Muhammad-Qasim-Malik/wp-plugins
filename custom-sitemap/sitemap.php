<?php
/**
 * Plugin Name: Custom Sitemap Generator
 * Description: A custom sitemap plugin for managing post URLs, sitemaps, and their relationships.
 * Version: 1.0
 * Author: Muhammad Qasim
 * License: GPL2
 */

// Create custom database table on plugin activation
function create_sitemap_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'sitemap_links';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        post_type VARCHAR(100) NOT NULL,
        link VARCHAR(255) NOT NULL,
        sitemap_name VARCHAR(255) NOT NULL,
        last_modified DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_sitemap_table');

function sitemap_generator_enqueue_scripts() {
    wp_enqueue_script('sitemap-generator-ajax', plugin_dir_url(__FILE__) . 'assets/js/handler.js', array('jquery'), null, true);

    wp_localize_script('sitemap-generator-ajax', 'sitemap_gen', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security_nonce' => wp_create_nonce('create_sitemap_nonce') 
    ));
}
add_action('admin_enqueue_scripts', 'sitemap_generator_enqueue_scripts');


$files_to_include = [
    'includes/menu.php',
    'includes/dashboard.php',
    'includes/sitemap-create/sitemap-create.php',
    'includes/sitemap-create/sitemap-file.php',
    'includes/sitemap-create/sitemap-helper.php',
    'includes/sitemap-post-links/sitemap-links.php',
    'includes/sitemap-add-link/sitemap-add-link.php',
    'includes/ajax-handler.php',
];


foreach ($files_to_include as $file) {
    $file_path = plugin_dir_path( __FILE__ ) . $file;  
    if ( file_exists( $file_path ) ) {
        require_once $file_path;
    } else {
        echo "File not found: $file <br>"; 
    }
}

?>
