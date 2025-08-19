<?php
namespace MQAB;

if ( ! defined( 'ABSPATH' ) ) exit;

class Admin {
    const OPTION_KEY = 'mqab_settings';

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'settings' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_color_picker' ] );
    }

    public static function menu() {
        add_options_page(
            'MQ Announcement Bar',
            'MQ Announcement Bar',
            'manage_options',
            'mqab-settings',
            [ __CLASS__, 'render' ]
        );
    }

    public static function settings() {
        register_setting( 'mqab_settings_group', self::OPTION_KEY, [
            'type'              => 'array',
            'sanitize_callback' => [ __CLASS__, 'sanitize' ],
            'default'           => [],
        ] );

        add_settings_section(
            'mqab_main',
            'Announcement Settings',
            '__return_false',
            'mqab-settings'
        );

        // Fields
        add_settings_field( 'text', 'Announcement Text', [ __CLASS__, 'field_text' ], 'mqab-settings', 'mqab_main' );
        add_settings_field( 'bg_color', 'Background Color', [ __CLASS__, 'field_bg' ], 'mqab-settings', 'mqab_main' );
        add_settings_field( 'text_color', 'Text Color', [ __CLASS__, 'field_color' ], 'mqab-settings', 'mqab_main' );
        add_settings_field( 'position', 'Position', [ __CLASS__, 'field_position' ], 'mqab-settings', 'mqab_main' );
        add_settings_field( 'dismiss', 'Dismiss Button', [ __CLASS__, 'field_dismiss' ], 'mqab-settings', 'mqab_main' );
    }

    public static function sanitize( $input ) {
        return [
            'text'       => sanitize_text_field( $input['text'] ?? '' ),
            'bg_color'   => sanitize_hex_color( $input['bg_color'] ?? '#0ea5e9' ),
            'text_color' => sanitize_hex_color( $input['text_color'] ?? '#ffffff' ),
            'position'   => in_array( $input['position'] ?? 'top', [ 'top', 'bottom' ], true ) ? $input['position'] : 'top',
            'dismiss'    => ! empty( $input['dismiss'] ) ? 1 : 0,
        ];
    }

    public static function field_text() {
        $opt = get_option( self::OPTION_KEY );
        echo '<input type="text" class="regular-text" name="' . self::OPTION_KEY . '[text]" value="' . esc_attr( $opt['text'] ?? '' ) . '" placeholder="Enter announcement text">';
    }

    public static function field_bg() {
        $opt = get_option( self::OPTION_KEY );
        echo '<input type="text" class="mqab-color-field" name="' . self::OPTION_KEY . '[bg_color]" value="' . esc_attr( $opt['bg_color'] ?? '#0ea5e9' ) . '" data-default-color="#0ea5e9" />';
    }

    public static function field_color() {
        $opt = get_option( self::OPTION_KEY );
        echo '<input type="text" class="mqab-color-field" name="' . self::OPTION_KEY . '[text_color]" value="' . esc_attr( $opt['text_color'] ?? '#ffffff' ) . '" data-default-color="#ffffff" />';
    }

    public static function field_position() {
        $opt = get_option( self::OPTION_KEY );
        $pos = $opt['position'] ?? 'top';
        echo '<select name="' . self::OPTION_KEY . '[position]">';
        echo '<option value="top" ' . selected( $pos, 'top', false ) . '>Top</option>';
        echo '<option value="bottom" ' . selected( $pos, 'bottom', false ) . '>Bottom</option>';
        echo '</select>';
    }

    public static function field_dismiss() {
        $opt = get_option( self::OPTION_KEY );
        $val = $opt['dismiss'] ?? 0;
        echo '<label><input type="checkbox" name="' . self::OPTION_KEY . '[dismiss]" value="1" ' . checked( $val, 1, false ) . '> Allow users to dismiss</label>';
    }

    public static function render() {
        echo '<div class="wrap"><h1>MQ Announcement Bar</h1><form method="post" action="options.php">';
        settings_fields( 'mqab_settings_group' );
        do_settings_sections( 'mqab-settings' );
        submit_button();
        echo '</form></div>';
        ?>
        <script>
            jQuery(document).ready(function ($) {
                $('.mqab-color-field').wpColorPicker();
                });
        </script>
        <?php
    }

    public static function enqueue_color_picker( $hook ) {
        if ( $hook !== 'settings_page_mqab-settings' ) return;
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'mqab-admin-color', MQAB_URL . 'assets/js/admin-color.js', [ 'wp-color-picker' ], false, true );
    }
}
