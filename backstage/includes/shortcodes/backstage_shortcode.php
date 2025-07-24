<?php 

function backstage_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'title' => get_the_title(),
        ), 
        $atts
    );


    $background_image = get_option('backstage_bg_image');
    $background_url = $background_image ? $background_image : '';

    $site_logo = wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full');
    
    $output = '<div class="backstage-container" style="background-image: url(' . esc_url($background_url) . ');">';
    $output .= '<div class="backstage-content">';
    $output .= '<p class="backstage-title">' . esc_html($atts['title']) . '</p>';
    $output .= '</div>';
    $output .= '<img src="' . esc_url($site_logo) . '" alt="Logo" class="backstage-logo">';
    $output .= '</div>';

    return $output;
}
add_shortcode('backstage', 'backstage_shortcode');
