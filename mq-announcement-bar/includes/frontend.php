<?php
namespace MQAB;

if ( ! defined( 'ABSPATH' ) ) exit;

class Frontend {
    public static function init() {
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'assets' ] );
        add_action( 'wp_footer', [ __CLASS__, 'render' ] );
    }

    public static function assets() {
        wp_enqueue_style( 'mqab-style', MQAB_URL . 'assets/style.css', [], filemtime( MQAB_DIR . 'assets/style.css' ) );
        wp_enqueue_script( 'mqab-script', MQAB_URL . 'assets/script.js', [], filemtime( MQAB_DIR . 'assets/script.js' ), true );
    }

    public static function render() {
        $opt = get_option( Admin::OPTION_KEY );
        if ( empty( $opt['text'] ) ) return;

        $style = sprintf(
            'background:%s;color:%s;',
            esc_attr( $opt['bg_color'] ?? '#0ea5e9' ),
            esc_attr( $opt['text_color'] ?? '#ffffff' )
        );

        $classes = 'mqab-bar mqab-' . esc_attr( $opt['position'] ?? 'top' );

        echo '<div id="mqab-bar" class="' . $classes . '" style="' . $style . '">';
        echo '<span class="mqab-text">' . esc_html( $opt['text'] ) . '</span>';
        if ( ! empty( $opt['dismiss'] ) ) {
            echo '<button class="mqab-dismiss">&times;</button>';
        }
        echo '</div>';
    }
}
