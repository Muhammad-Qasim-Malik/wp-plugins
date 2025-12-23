<?php
/**
 * Plugin Name: BrilliantSync (Procedural)
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BSYNC_PATH', plugin_dir_path( __FILE__ ) );
define( 'BSYNC_URL', plugin_dir_url( __FILE__ ) );


/**
 * Include Files
 */
$includes = [
    'includes/admin/admin-interface.php',
    // 'includes/helper.php',
];

if ( is_admin() ) {
    foreach($includes as $include){
        require_once(BSYNC_PATH . $include);
    }
}

/**
 * 2. ENQUEUE STYLES
 */
function bsync_enqueue_styles() {
    wp_enqueue_style('bsync-styles', BSYNC_URL . 'assets/css/admin-style.css', [], '1.0', 'all');
}
add_action('admin_enqueue_scripts', 'bsync_enqueue_styles');
