<?php
function handle_create_sitemap_ajax() {
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'create_sitemap_nonce')) {
        wp_send_json_error(['message' => 'Security check failed.']);
        
        wp_die();
    }

    $sitemap_name = sanitize_text_field($_POST['sitemap_name']);
    $post_type = sanitize_text_field($_POST['post_type']);
    $selected_posts = isset($_POST['selected_posts']) ? $_POST['selected_posts'] : [];

    global $wpdb;

    $existing_sitemap = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}sitemap_links WHERE sitemap_name = %s", $sitemap_name
    ));

    if ($existing_sitemap > 0) {
        wp_send_json_error(['message' => 'Sitemap name already exists.']);
    }

    if ($post_type !== 'custom') {
        create_sitemap_for_post_type($post_type, $sitemap_name);
    } else {
        create_custom_sitemap($selected_posts, $sitemap_name);
    }

    wp_send_json_success(['message' => 'Sitemap created successfully.']);
    wp_die();
}

add_action('wp_ajax_create_sitemap', 'handle_create_sitemap_ajax');
add_action('wp_ajax_nopriv_create_sitemap', 'handle_create_sitemap_ajax');

function edit_sitemap_link_ajax() {
    global $wpdb;

    if (isset($_POST['link_id']) && isset($_POST['new_sitemap_name'])) {
        $link_id = intval($_POST['link_id']);
        $new_sitemap_name = sanitize_text_field($_POST['new_sitemap_name']);
        $link_url = sanitize_text_field($_POST['link_url']); 

        $existing_link = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}sitemap_links WHERE id != %d AND sitemap_name = %s AND link = %s",
            $link_id, $new_sitemap_name, $link_url
        ));

        if ($existing_link > 0) {
            wp_send_json_error(['message' => 'This link already exists in the selected sitemap.']);
            return;
        }

        $wpdb->update(
            $wpdb->prefix . 'sitemap_links',
            ['sitemap_name' => $new_sitemap_name],
            ['id' => $link_id]
        );

        // Regenerate all sitemaps after update
        regenerate_all_sitemaps();

        wp_send_json_success(['message' => 'Sitemap updated successfully!']);
    } else {
        wp_send_json_error(['message' => 'Invalid request.']);
    }
}

add_action('wp_ajax_edit_sitemap_link', 'edit_sitemap_link_ajax');
add_action('wp_ajax_nopriv_edit_sitemap_link', 'edit_sitemap_link_ajax');

add_action('wp_ajax_get_posts_by_post_type', function(){
    check_ajax_referer('get_posts_nonce', 'security');

    $post_type = sanitize_text_field($_POST['post_type'] ?? '');

    if (!$post_type) {
        wp_send_json_error('No post type provided');
    }

    $posts = get_posts([
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $data = [];

    foreach($posts as $post) {
        $data[] = [
            'id' => $post->ID,
            'title' => $post->post_title,
        ];
    }

    wp_send_json_success($data);
});

add_action('wp_ajax_add_link', 'handle_add_link_ajax');
function handle_add_link_ajax() {
    error_log("Hello Pehle");
    if (!isset($_POST['add_link_nonce']) || !wp_verify_nonce($_POST['add_link_nonce'], 'add_link_action')) {
        wp_send_json_error(['message' => 'Nonce verification failed.']);
    }
    error_log("Hello Baad main");

    global $wpdb;

    $post_type = sanitize_text_field($_POST['post_type'] ?? '');
    $post_id = intval($_POST['post_id'] ?? 0);
    $sitemap_name = sanitize_text_field($_POST['sitemap_name'] ?? '');

    if (!$post_type || !$post_id || !$sitemap_name) {
        wp_send_json_error(['message' => 'Please fill in all fields.']);
    }

    $post = get_post($post_id);
    if (!$post || $post->post_type !== $post_type) {
        error_log("Invalid post or post_type mismatch. post_id: $post_id, expected: $post_type");
        wp_send_json_error(['message' => 'Invalid post selected.']);
    }

    $link = get_permalink($post_id);
    $last_modified = $post->post_modified;

    $table = $wpdb->prefix . 'sitemap_links';
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE link = %s AND sitemap_name = %s",
        $link, $sitemap_name
    ));

    if ($exists) {
        wp_send_json_error(['message' => 'This link already exists in the selected sitemap.']);
    }

    $inserted = $wpdb->insert(
        $table,
        [
            'post_type' => $post_type,
            'link' => $link,
            'sitemap_name' => $sitemap_name,
            'last_modified' => $last_modified,
        ],
        ['%s', '%s', '%s', '%s']
    );

    if ($inserted === false) {
        error_log("DB insert failed: " . $wpdb->last_error);
        wp_send_json_error(['message' => 'Failed to add link due to database error.']);
    }

    wp_send_json_success(['message' => 'Link added successfully!']);
}

