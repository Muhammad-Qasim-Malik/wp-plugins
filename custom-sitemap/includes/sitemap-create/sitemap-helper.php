<?php
function add_sitemap_meta_box() {
    $post_types = get_post_types(array('public' => true), 'names');
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'sitemap_meta_box',
            'Select Sitemap',
            'display_sitemap_meta_box',
            $post_type,
            'side',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'add_sitemap_meta_box');

function display_sitemap_meta_box($post) {
    global $wpdb;
    $sitemaps = $wpdb->get_results("SELECT DISTINCT sitemap_name FROM {$wpdb->prefix}sitemap_links");

    $selected_sitemap = get_post_meta($post->ID, '_selected_sitemap', true);
    $existing_sitemaps = $wpdb->get_col(
        $wpdb->prepare("SELECT sitemap_name FROM {$wpdb->prefix}sitemap_links WHERE link = %s", get_permalink($post->ID))
    );

    echo '<select name="selected_sitemap" id="selected_sitemap" class="postbox">';
    echo '<option value="">Select Sitemap</option>';
    foreach ($sitemaps as $sitemap) {
        $selected = in_array($sitemap->sitemap_name, $existing_sitemaps) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($sitemap->sitemap_name) . '" ' . $selected . '>' . esc_html($sitemap->sitemap_name) . '</option>';
    }
    echo '</select>';
}

function save_sitemap_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['selected_sitemap'])) {
        $selected_sitemap = sanitize_text_field($_POST['selected_sitemap']);
        update_post_meta($post_id, '_selected_sitemap', $selected_sitemap);
    }
}
add_action('save_post', 'save_sitemap_meta');

function add_post_to_sitemap($post_id, $post_type) {
    global $wpdb;

    $post_url = get_permalink($post_id);
    $selected_sitemap = get_post_meta($post_id, '_selected_sitemap', true);

    if (empty($selected_sitemap)) {
        $selected_sitemap = 'default_links';
    }

    $existing_post = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}sitemap_links WHERE link = %s AND sitemap_name = %s", $post_url, $selected_sitemap)
    );

    if ($existing_post == 0) {
        $wpdb->insert(
            $wpdb->prefix . 'sitemap_links',
            [
                'post_type' => $post_type,
                'link' => $post_url,
                'sitemap_name' => $selected_sitemap,
                'last_modified' => current_time('mysql'),
            ]
        );
    }
}

function regenerate_sitemaps_on_publish($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if ('publish' !== get_post_status($post_id)) return;

    $post_type = get_post_type($post_id);
    add_post_to_sitemap($post_id, $post_type);
    regenerate_all_sitemaps();
}
add_action('save_post', 'regenerate_sitemaps_on_publish');

function regenerate_all_sitemaps() {
    global $wpdb;
    $sitemaps = $wpdb->get_results("SELECT DISTINCT sitemap_name FROM {$wpdb->prefix}sitemap_links");

    foreach ($sitemaps as $sitemap) {
        create_sitemap_file($sitemap->sitemap_name);
    }
}