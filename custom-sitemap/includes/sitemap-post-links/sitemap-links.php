<?php
function manage_links_page() {
    global $wpdb;

    // Handle multiple deletions if POSTed
    if (isset($_POST['delete_selected']) && !empty($_POST['selected_links'])) {
        $selected_links = array_map('intval', $_POST['selected_links']);
        if (!empty($selected_links)) {
            foreach ($selected_links as $link_id) {
                $wpdb->delete("{$wpdb->prefix}sitemap_links", ['id' => $link_id], ['%d']);
            }
            regenerate_all_sitemaps();
            echo '<div class="updated"><p>Selected links deleted successfully!</p></div>';
        }
    }

    if (isset($_GET['delete'])) {
        $link_id = intval($_GET['delete']);
        if ($link_id > 0) {
            $wpdb->delete("{$wpdb->prefix}sitemap_links", ['id' => $link_id], ['%d']);
            regenerate_all_sitemaps();
            echo '<script>window.location.href = "' . admin_url('admin.php?page=manage-links') . '";</script>';
            exit;
        }
    }

    $sitemaps = $wpdb->get_results("SELECT DISTINCT sitemap_name FROM {$wpdb->prefix}sitemap_links");
    $sitemap_name = isset($_GET['sitemap_name']) ? sanitize_text_field($_GET['sitemap_name']) : 'All';

    if ($sitemap_name !== 'All') {
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}sitemap_links WHERE sitemap_name = %s", $sitemap_name);
    } else {
        $query = "SELECT * FROM {$wpdb->prefix}sitemap_links";
    }

    $results = $wpdb->get_results($query);
    ?>
    <div class="wrap">
        <h1>Manage Sitemap Links</h1>

        <form method="get" action="<?php echo admin_url('admin.php'); ?>">
            <input type="hidden" name="page" value="manage-links" />
            <label for="sitemap_name">Filter by Sitemap: </label>
            <select name="sitemap_name" id="sitemap_name" class="postbox">
                <option value="All" <?php selected($sitemap_name, 'All'); ?>>All</option>
                <?php
                foreach ($sitemaps as $sitemap) {
                    echo '<option value="' . esc_attr($sitemap->sitemap_name) . '" ' . selected($sitemap_name, $sitemap->sitemap_name, false) . '>' . esc_html($sitemap->sitemap_name) . '</option>';
                }
                ?>
            </select>
            <button type="submit">Filter</button>
        </form>

        <!-- Form to handle bulk delete -->
        <form method="post" onsubmit="return confirm('Are you sure you want to delete selected links?');">
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" /></th> <!-- Select All checkbox -->
                        <th>Post Type</th>
                        <th>Link</th>
                        <th>Sitemap Name</th>
                        <th>Last Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $link) { ?>
                        <tr>
                            <td><input type="checkbox" name="selected_links[]" value="<?php echo intval($link->id); ?>" /></td>
                            <td><?php echo esc_html($link->post_type); ?></td>
                            <td><a href="<?php echo esc_url($link->link); ?>" target="_blank"><?php echo esc_html($link->link); ?></a></td>
                            <td><?php echo esc_html($link->sitemap_name); ?></td>
                            <td><?php echo esc_html($link->last_modified); ?></td>
                            <td>
                                <button class="edit-sitemap-btn" 
                                        data-link-id="<?php echo $link->id; ?>" 
                                        data-current-sitemap="<?php echo esc_attr($link->sitemap_name); ?>"
                                        data-link-url="<?php echo esc_attr($link->link); ?>">Edit</button> | 
                                <a href="?page=manage-links&delete=<?php echo $link->id; ?>" onclick="return confirm('Are you sure you want to delete this link?');">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <button type="submit" name="delete_selected" class="button button-danger" style="margin-top: 10px;">Delete Selected</button>
        </form>
    </div>

    <!-- Modal Popup for editing sitemap -->
    <div id="sitemap-modal" style="display:none;">
        <div class="overlay"></div>
        <div class="modal-content">
            <h2>Select New Sitemap</h2>
            <form id="sitemap-edit-form">
                <input type="hidden" name="link_id" id="link_id" />
                <input type="hidden" name="link_url" id="link_url" /> <!-- Hidden field for link URL -->
                <label for="new_sitemap_name">New Sitemap Name:</label>
                <select name="new_sitemap_name" id="new_sitemap_name" required>
                    <?php
                    foreach ($sitemaps as $sitemap) {
                        echo '<option value="' . esc_attr($sitemap->sitemap_name) . '">' . esc_html($sitemap->sitemap_name) . '</option>';
                    }
                    ?>
                </select>
                <button type="submit">Update Sitemap</button>
                <button type="button" id="close-modal">Cancel</button>
            </form>
        </div>
    </div>

    <style>
        /* Modal styles */
        #sitemap-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: none;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            width: 400px;
        }

        .modal-content button {
            margin-top: 10px;
        }
    </style>

    <script>
        // Select/Deselect all checkboxes
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('input[name="selected_links[]"]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
    </script>

<?php
}
?>
