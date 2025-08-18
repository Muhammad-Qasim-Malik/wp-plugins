<?php
namespace MQRT;

if ( ! defined( 'ABSPATH' ) ) exit;

class Frontend {
    public static function init() {
        add_filter( 'the_content', [ __CLASS__, 'prepend_reading_time' ] );
    }

    public static function prepend_reading_time( $content ) {
        if ( ! is_singular( 'post' ) ) return $content;

        $wpm = (int) get_option( Admin::OPTION_KEY, 200 );
        $words = str_word_count( wp_strip_all_tags( $content ) );
        $minutes = max( 1, ceil( $words / $wpm ) );

        $html = '<div class="mqrt-reading-time" style="margin-bottom:10px;padding:8px 12px;background:#f8fafc;border-left:4px solid #0ea5e9;">
            ⏱️ Estimated reading time: <strong>' . $minutes . ' min read</strong>
        </div>';

        return $html . $content;
    }
}
