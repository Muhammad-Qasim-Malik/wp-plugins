<?php
/**
 * Plugin Name: MQ Elementor Widgets
 * Description: Custom Elementor Widgets Plugin
 * Version: 1.0
 * Author: Muhammad Qasim
 * Text Domain: mq-elementor-widgets
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit;

function mq_filler_card_widget_assets() {

    wp_enqueue_style(
        'mq-filler-card-widget-style', 
        plugin_dir_url( __FILE__ ) . 'assets/css/style.css', 
        [], 
        '1.0.0', 
        'all'
    );

    wp_enqueue_script(
        'mq-filler-card-widget-script', 
        plugin_dir_url( __FILE__ ) . 'assets/js/script.js', 
        ['jquery'], 
        '1.0.0',
        true 
    );
}

add_action('wp_enqueue_scripts', 'mq_filler_card_widget_assets');


function mq_elementor_widget_check_dependency() {
    if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
        add_action( 'admin_notices', 'mq_elementor_widget_dependency_notice' );
        return false;
    }
    return true;
}

        
function mq_elementor_widget_dependency_notice() {
    echo '<div class="error"><p><strong>' . __( 'MQ Elementor Widgets', 'mq-elementor-widgets' ) . '</strong> ' . __( 'requires Elementor to be installed and activated.', 'mq-elementor-widgets' ) . '</p></div>';
}

if ( mq_elementor_widget_check_dependency() ) {
    
    add_action( 'elementor/widgets/widgets_registered', function() {
        require_once( __DIR__ . '/widgets/heading-widget.php' );
        require_once( __DIR__ . '/widgets/button-widget.php' );
        require_once( __DIR__ . '/widgets/filler-card-widget.php' );
    });
}


