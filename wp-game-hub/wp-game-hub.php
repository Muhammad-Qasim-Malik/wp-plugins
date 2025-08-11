<?php
/*
Plugin Name: WP Game Hub
Description: A collection of games for WordPress with Elementor widget and shortcode support.
Version: 1.0
Author: Muhammad Qasim
Text Domain: wp-game-hub
*/

if (!defined('ABSPATH')) {
    exit; 
}


define('GAMEHUB_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('GAMEHUB_PLUGIN_URL', plugin_dir_url(__FILE__));  

$gamehub_includes = array(
    'includes/shortcodes/sudoku.php',
);

foreach ($gamehub_includes as $file) {
    include_once(GAMEHUB_PLUGIN_PATH . $file);
}

function gamehub_enqueue_scripts() {
    wp_enqueue_style('gamehub-sudoku-style', GAMEHUB_PLUGIN_URL . 'assets/css/sudoku-style.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('gamehub-sudoku-script', GAMEHUB_PLUGIN_URL . 'assets/js/sudoku-script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'gamehub_enqueue_scripts');

function gamehub_register_widgets($widgets_manager) {
    if (class_exists('Elementor\Widget_Base')) {
        $gamehub_includes = array(
            'includes/widgets/sudoku-widget.php'
        );

        foreach ($gamehub_includes as $file) {
            include_once(GAMEHUB_PLUGIN_PATH . $file);
        }
        $widgets_manager->register(new \Elementor_Sudoku_Widget());
    }
}
add_action('elementor/widgets/register', 'gamehub_register_widgets');
