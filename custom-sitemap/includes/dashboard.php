<?php
function sitemap_generator_page() {
    global $wpdb;

    // Get all distinct sitemaps from the database
    $sitemaps = $wpdb->get_results("SELECT DISTINCT sitemap_name FROM {$wpdb->prefix}sitemap_links");

    echo '<h1>Custom Sitemap Generator</h1>';
    echo '<p>Welcome to the custom sitemap generator plugin!</p>';
    if(isset($_GET['delete_sitemap'])){
        echo '<div class="updated"><p>Sitemap "' . esc_html($_GET['delete_sitemap']) . '" deleted successfully!</p></div>';
    }
    // Display all created sitemaps
    if ($sitemaps) {
        echo '<h2>Created Sitemaps</h2>';
        echo '<table class="wp-list-table widefat fixed striped posts">';
        echo '<thead>';
        echo '<tr><th>Sitemap Name</th><th>Sitemap Link</th><th>Number of Links</th><th>Actions</th></tr>';
        echo '</thead><tbody>';

        // Loop through the sitemaps and display them
        foreach ($sitemaps as $sitemap) {
            // Get the number of links for each sitemap
            $num_links = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}sitemap_links WHERE sitemap_name = %s",
                    $sitemap->sitemap_name
                )
            );

            // Get the URL for the sitemap (based on the name)
            $sitemap_url = home_url('/') . 'wp-content/uploads/sitemaps/' . urlencode($sitemap->sitemap_name) . '.xml';

            echo '<tr>';
            echo '<td>' . esc_html($sitemap->sitemap_name) . '</td>';
            echo '<td><a href="' . esc_url($sitemap_url) . '" target="_blank">' . esc_html($sitemap_url) . '</a></td>';
            echo '<td>' . esc_html($num_links) . '</td>';
            echo '<td><a href="' . admin_url('admin.php?page=sitemap-generator&delete_sitemap=' . urlencode($sitemap->sitemap_name)) . '" onclick="return confirm(\'Are you sure you want to delete this Sitemap?\');">Delete</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>No sitemaps have been created yet.</p>';
    }

    // Handle deleting a sitemap
    if (isset($_GET['delete_sitemap'])) {
        $sitemap_to_delete = sanitize_text_field($_GET['delete_sitemap']);
        $wpdb->delete(
            $wpdb->prefix . 'sitemap_links',
            ['sitemap_name' => $sitemap_to_delete],
            ['%s']
        );

        
        // Redirect back to avoid re-submit
        // echo '<script>window.location.href = "' . admin_url('admin.php?page=sitemap-generator') . '";</script>';
    }
}