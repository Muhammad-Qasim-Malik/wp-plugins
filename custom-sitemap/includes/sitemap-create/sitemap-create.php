<?php
function create_sitemap_page() {
    ?>
    <div class="wrap">
        <h1>Create Sitemap</h1>
        <form id="create-sitemap-form" method="post">
            <?php wp_nonce_field('create_sitemap_action', 'create_sitemap_nonce'); ?>

            <label for="sitemap_name">Sitemap Name: </label>
            <input type="text" name="sitemap_name" id="sitemap_name" required />

            <label for="post_type">Post Type: </label>
            <select name="post_type" id="post_type">
                <?php
                // Get all registered post types
                $post_types = get_post_types(['public' => true], 'objects');
                
                // Loop through all post types and add them to the dropdown
                foreach ($post_types as $post_type_object) {
                    echo '<option value="' . esc_attr($post_type_object->name) . '">' . esc_html($post_type_object->label) . '</option>';
                }
                ?>
                <option value="custom">Custom Sitemap</option>
            </select>

            <div id="custom-posts-section" style="display:none;">
                <h3>Select Posts for Custom Sitemap</h3>
                <?php
                // Retrieve all posts for custom sitemap creation
                $posts = get_posts(['post_type' => 'post', 'posts_per_page' => -1]);
                foreach ($posts as $post) {
                    echo '<label>';
                    echo '<input type="checkbox" name="selected_posts[]" value="' . $post->ID . '"> ' . $post->post_title;
                    echo '</label><br>';
                }
                ?>
            </div>

            <input type="submit" value="Generate Sitemap">
        </form>
        
        <!-- Display response message (success/error) -->
        <div class="response-message"></div>
    </div>

    <script type="text/javascript">
        // Toggle custom posts section visibility
        document.getElementById('post_type').addEventListener('change', function() {
            if (this.value === 'custom') {
                document.getElementById('custom-posts-section').style.display = 'block';
            } else {
                document.getElementById('custom-posts-section').style.display = 'none';
            }
        });
    </script>
    <?php
}
function create_sitemap_for_post_type($post_type, $sitemap_name) {
    global $wpdb;
    $posts = get_posts([
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);
    foreach ($posts as $post) {
        $wpdb->insert($wpdb->prefix . 'sitemap_links', [
            'post_type' => $post_type,
            'link' => get_permalink($post->ID),
            'sitemap_name' => $sitemap_name,
            'last_modified' => $post->post_modified,
        ]);
    }
    create_sitemap_file($sitemap_name);
    wp_redirect(admin_url('admin.php?page=create-sitemap'));
    exit;
}

function create_custom_sitemap($selected_posts, $sitemap_name) {
    global $wpdb;
    if (empty($selected_posts)) {
        echo '<div class="error"><p>No posts selected for the custom sitemap.</p></div>';
        return;
    }
    foreach ($selected_posts as $post_id) {
        $post = get_post($post_id);
        if ($post) {
            $wpdb->insert($wpdb->prefix . 'sitemap_links', [
                'post_type' => $post->post_type,
                'link' => get_permalink($post->ID),
                'sitemap_name' => $sitemap_name,
                'last_modified' => $post->post_modified,
            ]);
        }
    }
    create_sitemap_file($sitemap_name);
    echo '<div class="updated"><p>Custom sitemap created successfully!</p></div>';
}
