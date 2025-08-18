<?php
namespace MQRT;

if ( ! defined( 'ABSPATH' ) ) exit;

class Admin {
    const OPTION_KEY = 'mqrt_words_per_minute';

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'settings' ] );
    }

    public static function menu() {
        add_options_page(
            'MQ Reading Time',
            'MQ Reading Time',
            'manage_options',
            'mqrt-settings',
            [ __CLASS__, 'render' ]
        );
    }

    public static function settings() {
        register_setting( 'mqrt_settings', self::OPTION_KEY, [
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 60, 
        ]);

        add_settings_section(
            'mqrt_main',
            'Reading Time Settings',
            function() {
                echo '<p>Set the words-per-minute speed for calculating estimated reading time.</p>';
            },
            'mqrt-settings'
        );

        add_settings_field(
            self::OPTION_KEY,
            'Words per Minute',
            function() {
                $val = get_option( self::OPTION_KEY, 200 );
                echo '<input type="number" min="50" max="1000" name="' . esc_attr( self::OPTION_KEY ) . '" value="' . esc_attr( $val ) . '" /> words/minute';
            },
            'mqrt-settings',
            'mqrt_main'
        );
    }

    public static function render() {
        if ( ! current_user_can( 'manage_options' ) ) return;

        echo '<div class="wrap"><h1>MQ Reading Time</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields( 'mqrt_settings' );
        do_settings_sections( 'mqrt-settings' );
        submit_button( 'Save Changes' );
        echo '</form></div>';
    }
}
