<?php

function mq_dashboard_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>You must be logged in to view the dashboard.</p>';
    }

    $current_user = wp_get_current_user();
    $active_tab = 'tab1'; // Default active tab is "My Academies"

    if (isset($_GET['edit_post_id'])) {
        $active_tab = 'tab3'; // Set "Edit Academy" as active tab if "edit_post_id" is set
    }

    ob_start(); ?>

    <div class="mq-dashboard">
        <div class="mq-tabs">
            <ul>
                <li class="<?php echo $active_tab === 'tab1' ? 'active' : ''; ?>" data-tab="tab1">My Academies</li>
                <li class="<?php echo $active_tab === 'tab2' ? 'active' : ''; ?>" data-tab="tab2">Add Academy</li>
                <?php if (isset($_GET['edit_post_id'])) { ?>
                    <li class="<?php echo $active_tab === 'tab3' ? 'active' : ''; ?>" data-tab="tab3">Edit Academy</li>
                <?php } ?>
                <li class="<?php echo $active_tab === 'tab4' ? 'active' : ''; ?>" data-tab="tab4">Settings</li>
                <li class="<?php echo $active_tab === 'tab5' ? 'active' : ''; ?>" data-tab="tab5">Logout</li>
            </ul>
        </div>

        <div class="mq-tab-content">
            <div id="tab1" class="mq-tab-pane <?php echo $active_tab === 'tab1' ? 'active' : ''; ?>">
                <h2>My Academies</h2>
                <div class="muay-thai-posts">
                    <?php mq_display_user_posts(); ?>
                </div>
            </div>

            <div id="tab2" class="mq-tab-pane <?php echo $active_tab === 'tab2' ? 'active' : ''; ?>">
                <h2>Add New Academy</h2>
                <div class="muay-thai-form">
                    <?php echo mq_add_academy_form(); ?>
                </div>
            </div>

            <div id="tab3" class="mq-tab-pane <?php echo $active_tab === 'tab3' ? 'active' : ''; ?>">
                <h2>Edit Academy</h2>
                <div class="muay-thai-form">
                    <?php echo mq_edit_academy_form(); ?>
                </div>
            </div>

            <div id="tab4" class="mq-tab-pane <?php echo $active_tab === 'tab4' ? 'active' : ''; ?>">
                <h2>Settings</h2>
                <p><?php echo mq_settings_form(); ?></p>
            </div>

            <div id="tab5" class="mq-tab-pane <?php echo $active_tab === 'tab5' ? 'active' : ''; ?>">
                <h2>Logout</h2>
                <a href="<?php echo wp_logout_url(home_url()); ?>">Click here to logout</a>
            </div>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('mq_dashboard', 'mq_dashboard_shortcode');



function mq_display_user_posts() {
    if (!is_user_logged_in()) return;

    $current_user = wp_get_current_user();
    $args = [
        'post_type' => 'muay-thai',  
        'author' => $current_user->ID,
        'posts_per_page' => 5, 
        'paged' => get_query_var('paged', 1), 
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ?>
        <table class="muay-thai-posts-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($query->have_posts()) {
                    $query->the_post(); 
                    // error_log(get_delete_post_link(get_the_ID()));
                    ?>
                    <tr>
                        <td><a href="<?php the_permalink(); ?>" class="mq-title"><?php the_title(); ?></a></td>
                        <td>
                            <a href="<?php echo esc_url(add_query_arg('edit_post_id', get_the_ID(), home_url(add_query_arg(array())))); ?>" class="mq-button">Edit</a> |
                            <a href="<?php echo esc_url(add_query_arg('delete_post_id', get_the_ID(), home_url(add_query_arg(array())))); ?>" class="mq-button mq-button-red" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php
            echo paginate_links([
                'total' => $query->max_num_pages,
                'current' => get_query_var('paged', 1),
                'end_size' => 3,         
                'mid_size' => 2,         
                'show_all' => false,  
            ]);
            ?>
        </div>

        <?php
        wp_reset_postdata();
    } else {
        echo '<p>You have no Muay-Thai posts yet.</p>';
    }
}

add_action('init', function() {
    if (isset($_GET['delete_post_id'])) {
        $post_id = intval($_GET['delete_post_id']); 
        
        $result = mq_delete_post($post_id); 
        
        if (is_wp_error($result)) {
            wp_die($result->get_error_message());
        } else {
            wp_redirect(home_url('/dashboard')); 
            exit;
        }
    }
});

function mq_delete_post($post_id) {
    if (!is_user_logged_in()) {
        return new WP_Error('not_logged_in', 'You must be logged in to delete posts.');
    }

    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'muay-thai') {
        return new WP_Error('invalid_post', 'Invalid post or incorrect post type.');
    }

    if ($post->post_author != get_current_user_id() && !current_user_can('administrator')) {
        return new WP_Error('no_permission', 'You do not have permission to delete this post.');
    }

    $deleted_post = wp_delete_post($post_id, true);

    if ($deleted_post) {
        return true;
    } else {
        return new WP_Error('delete_failed', 'The post could not be deleted.');
    }
}

function mq_add_academy_form() {
    if (!is_user_logged_in()) {
        return '<p>You must be logged in to add an academy.</p>';
    }

    $current_user = wp_get_current_user();
    $role = $current_user->roles[0];  
    ob_start();
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="academy_title">Academy Title:</label>
        <input type="text" name="academy_title" id="academy_title" required /><br><br>

        <label for="academy_featured_image">Featured Image:</label>
        <input type="file" name="academy_featured_image" id="academy_featured_image" required /><br><br>

        <label for="academy_location">Location:</label>
        <input type="text" name="academy_location" id="academy_location" required /><br><br>

        <label for="academy_phone">Phone:</label>
        <input type="text" name="academy_phone" id="academy_phone" required /><br><br>

        <label for="academy_website">Website:</label>
        <input type="text" name="academy_website" id="academy_website" /><br><br>

        <label for="muay_thai_location">Location (Category):</label>
        <?php
        $terms = get_terms([
            'taxonomy' => 'muay-thai-location',
            'orderby'  => 'name',
            'hide_empty' => false,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            echo '<select name="muay_thai_location" id="muay_thai_location">';
            echo '<option value="">Select a Location</option>';
            foreach ($terms as $term) {
                echo '<option value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
            }
            echo '</select>';
        }
        ?><br><br>

        <?php if ($role == 'paid_member') { ?>
            <label for="academy_images">Multiple Images:</label>
            <input type="file" name="academy_images[]" id="academy_images" multiple /><br><br>

            <label for="academy_calendar_link">Calendar Link:</label>
            <input type="url" name="academy_calendar_link" id="academy_calendar_link" /><br><br>

            <label for="academy_short_bio">Short Bio:</label>
            <textarea name="academy_short_bio" id="academy_short_bio"></textarea><br><br>
        <?php } ?>

        <input type="submit" name="submit_academy" class="mq-button" value="Submit Academy" />
    </form>
    <?php
    return ob_get_clean();
}

function mq_handle_academy_form_submission() {
    if (!is_user_logged_in()) return;

    if (isset($_POST['submit_academy'])) {
        $current_user = wp_get_current_user();
        $role = $current_user->roles[0];  // Get the current user's role (either 'free_member' or 'paid_member')

        // Get the data from the form
        $academy_title = sanitize_text_field($_POST['academy_title']);
        $academy_location = sanitize_text_field($_POST['academy_location']);
        $academy_phone = sanitize_text_field($_POST['academy_phone']);
        $academy_website = sanitize_text_field($_POST['academy_website']);
        $muay_thai_location = isset($_POST['muay_thai_location']) ? intval($_POST['muay_thai_location']) : 0; 

        // Prepare post data
        $post_data = array(
            'post_title'   => $academy_title,
            'post_content' => '',  // You can add content here if needed
            'post_status'  => 'publish',
            'post_type'    => 'muay-thai', // Assuming 'muay-thai' is the custom post type
            'post_author'  => $current_user->ID,
        );

        // Insert the new post
        $post_id = wp_insert_post($post_data);

        if ($post_id) {
            // Save the custom fields for all users
            update_post_meta($post_id, 'muay_thai_location', $academy_location);
            update_post_meta($post_id, 'muay_thai_phone', $academy_phone);
            update_post_meta($post_id, 'muay_thai_website', $academy_website);
            if ($muay_thai_location) {
                wp_set_object_terms($post_id, $muay_thai_location, 'muay-thai-location');
            }

            if ($role == 'paid_member') {
                if (!empty($_FILES['academy_images']['name'][0])) {
                    $image_ids = [];
                    $files = $_FILES['academy_images'];

                    // Loop through the files and upload each one
                    foreach ($files['name'] as $key => $value) {
                        if ($files['name'][$key]) {
                            $file = [
                                'name' => $files['name'][$key],
                                'type' => $files['type'][$key],
                                'tmp_name' => $files['tmp_name'][$key],
                                'error' => $files['error'][$key],
                                'size' => $files['size'][$key],
                            ];

                            // Use wp_handle_upload to handle file upload
                            $upload_overrides = ['test_form' => false];
                            $uploaded_file = wp_handle_upload($file, $upload_overrides);

                            if (!isset($uploaded_file['error'])) {
                                // Add the file to WordPress media library
                                $attachment = array(
                                    'guid' => $uploaded_file['url'], 
                                    'post_mime_type' => $uploaded_file['type'],
                                    'post_title' => basename($uploaded_file['file']),
                                    'post_content' => '',
                                    'post_status' => 'inherit'
                                );
                                $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file'], $post_id);
                                $image_ids[] = $attachment_id;
                            } else {
                                // Handle upload error
                                echo 'Error uploading file: ' . $uploaded_file['error'];
                            }
                        }
                    }

                    // Update post meta with image IDs
                    update_post_meta($post_id, 'muay_thai_multiple_images', $image_ids);
                }

                update_post_meta($post_id, 'muay_thai_calendar_link', sanitize_text_field($_POST['academy_calendar_link']));
                update_post_meta($post_id, 'muay_thai_short_bio', sanitize_textarea_field($_POST['academy_short_bio']));
            }

            // Handle Featured Image
            if (!empty($_FILES['academy_featured_image']['name'])) {
                $file = $_FILES['academy_featured_image'];
                $upload_overrides = ['test_form' => false];
                $uploaded_file = wp_handle_upload($file, $upload_overrides);

                if (!isset($uploaded_file['error'])) {
                    // Add the file to WordPress media library and set as featured image
                    $attachment = array(
                        'guid' => $uploaded_file['url'],
                        'post_mime_type' => $uploaded_file['type'],
                        'post_title' => basename($uploaded_file['file']),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file'], $post_id);
                    set_post_thumbnail($post_id, $attachment_id);
                } else {
                    // Handle upload error
                    echo 'Error uploading featured image: ' . $uploaded_file['error'];
                }
            }

            // Redirect to the newly created post
            wp_redirect(get_permalink($post_id));
            exit;
        }
    }
}
add_action('init', 'mq_handle_academy_form_submission');

function mq_edit_academy_form() {
    if (!is_user_logged_in()) {
        return '<p>You must be logged in to edit this academy.</p>';
    }

    $post_id = isset($_GET['edit_post_id']) ? intval($_GET['edit_post_id']) : 0;
    $post = get_post($post_id);

    if (!$post || $post->post_author != get_current_user_id() || $post->post_type != 'muay-thai') {
        return '<p>You are not authorized to edit this academy or the post does not exist.</p>';
    }


    ob_start();
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="academy_title">Academy Title:</label>
        <input type="text" name="academy_title" id="academy_title" value="<?php echo esc_attr($post->post_title); ?>" required /><br><br>

        <label for="academy_featured_image">Featured Image:</label>
        <?php
        $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
        if ($featured_image_url) {
            echo '<img src="' . esc_url($featured_image_url) . '" alt="Featured Image" style="max-width:200px;"/><br>';
        }
        ?>
        <input type="file" name="academy_featured_image" id="academy_featured_image" /><br><br>

        <label for="academy_location">Location:</label>
        <input type="text" name="academy_location" id="academy_location" value="<?php echo esc_attr(get_post_meta($post_id, 'muay_thai_location', true)); ?>" required /><br><br>

        <label for="academy_phone">Phone:</label>
        <input type="text" name="academy_phone" id="academy_phone" value="<?php echo esc_attr(get_post_meta($post_id, 'muay_thai_phone', true)); ?>" required /><br><br>

        <label for="academy_website">Website:</label>
        <input type="text" name="academy_website" id="academy_website" value="<?php echo esc_attr(get_post_meta($post_id, 'muay_thai_website', true)); ?>" /><br><br>

        <label for="muay_thai_location">Location (Category):</label>
        <?php
        $terms = get_terms([
            'taxonomy' => 'muay-thai-location',
            'orderby'  => 'name',
            'hide_empty' => false,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            $current_terms = wp_get_post_terms($post_id, 'muay-thai-location');
            $selected_term_ids = wp_list_pluck($current_terms, 'term_id');

            echo '<select name="muay_thai_location" id="muay_thai_location">';
            echo '<option value="">Select a Location</option>';
            foreach ($terms as $term) {
                $selected = in_array($term->term_id, $selected_term_ids) ? ' selected' : '';
                echo '<option value="' . esc_attr($term->term_id) . '"' . $selected . '>' . esc_html($term->name) . '</option>';
            }
            echo '</select>';
        }
        ?><br><br>

        <?php
        $current_user = wp_get_current_user();
        $role = $current_user->roles[0];

        if ($role == 'paid_member') { ?>
            <label for="academy_images">Multiple Images:</label>
            <?php
            $multiple_images = get_post_meta($post_id, 'muay_thai_multiple_images', true);
            if ($multiple_images) {
                foreach ($multiple_images as $image_id) {
                    $image_url = wp_get_attachment_url($image_id);
                    echo '<img src="' . esc_url($image_url) . '" alt="Image" style="max-width:100px; margin-right: 10px;" />';
                }
            }
            ?>
            <input type="file" name="academy_images[]" id="academy_images" multiple /><br><br>

            <label for="academy_calendar_link">Calendar Link:</label>
            <input type="url" name="academy_calendar_link" id="academy_calendar_link" value="<?php echo esc_url(get_post_meta($post_id, 'muay_thai_calendar_link', true)); ?>" /><br><br>

            <label for="academy_short_bio">Short Bio:</label>
            <textarea name="academy_short_bio" id="academy_short_bio"><?php echo esc_textarea(get_post_meta($post_id, 'muay_thai_short_bio', true)); ?></textarea><br><br>
        <?php } ?>

        <input type="submit" name="submit_academy_edit" class="mq-button" value="Update Academy" />
    </form>
    <?php
    return ob_get_clean();
}


function mq_handle_academy_edit_submission() {
    if (isset($_POST['submit_academy_edit'])) {
        $post_id = isset($_GET['edit_post_id']) ? intval($_GET['edit_post_id']) : 0;
        $post = get_post($post_id);

        if (!$post || $post->post_author != get_current_user_id()) {
            echo '<p>You are not authorized to edit this academy.</p>';
            return;
        }

        // Get the form data
        $academy_title = sanitize_text_field($_POST['academy_title']);
        $academy_location = intval($_POST['muay_thai_location']); 
        $academy_phone = sanitize_text_field($_POST['academy_phone']);
        $academy_website = sanitize_text_field($_POST['academy_website']);
        
        $updated_post = array(
            'ID' => $post_id,
            'post_title' => $academy_title,
            'post_content' => $post->post_content, 
        );
        wp_update_post($updated_post);

        // Update custom fields
        update_post_meta($post_id, 'muay_thai_phone', $academy_phone);
        update_post_meta($post_id, 'muay_thai_website', $academy_website);

        // Set the selected category (taxonomy)
        if ($academy_location) {
            wp_set_post_terms($post_id, [$academy_location], 'muay-thai-location');
        }

        // Handle featured image (if applicable)
        if (!empty($_FILES['academy_featured_image']['name'])) {
            $file = $_FILES['academy_featured_image'];
            $upload_overrides = ['test_form' => false];
            $uploaded_file = wp_handle_upload($file, $upload_overrides);
            if (!isset($uploaded_file['error'])) {
                $attachment = array(
                    'guid' => $uploaded_file['url'],
                    'post_mime_type' => $uploaded_file['type'],
                    'post_title' => basename($uploaded_file['file']),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file'], $post_id);
                set_post_thumbnail($post_id, $attachment_id);
            } else {
                echo '<p>Error uploading featured image: ' . $uploaded_file['error'] . '</p>';
            }
        }

        if (!empty($_FILES['academy_images']['name'][0])) {
            $files = $_FILES['academy_images'];
            $image_ids = [];

            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = [
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key],
                    ];

                    $upload_overrides = ['test_form' => false];
                    $uploaded_file = wp_handle_upload($file, $upload_overrides);

                    if (!isset($uploaded_file['error'])) {
                        $attachment = array(
                            'guid' => $uploaded_file['url'],
                            'post_mime_type' => $uploaded_file['type'],
                            'post_title' => basename($uploaded_file['file']),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file'], $post_id);
                        $image_ids[] = $attachment_id;
                    }
                }
            }
            update_post_meta($post_id, 'muay_thai_multiple_images', $image_ids);
        }

        if (!empty($_POST['academy_calendar_link'])) {
            update_post_meta($post_id, 'muay_thai_calendar_link', sanitize_text_field($_POST['academy_calendar_link']));
        }

        if (!empty($_POST['academy_short_bio'])) {
            update_post_meta($post_id, 'muay_thai_short_bio', sanitize_textarea_field($_POST['academy_short_bio']));
        }
        wp_redirect(home_url('/dashboard'));
        exit;
        // echo '<p>Academy updated successfully!</p>';
    }
}
add_action('init', 'mq_handle_academy_edit_submission');


function mq_settings_form() {
    if (!is_user_logged_in()) {
        return '<p>You must be logged in to edit your profile.</p>';
    }
    $current_user = wp_get_current_user();

    if (!$current_user || !isset($current_user->ID)) {
        return '<p>User not found.</p>';
    }

    ob_start(); ?>

    <form action="" method="post">
        <h2>Edit Profile</h2>
        
        <label for="user_name">Full Name:</label>
        <input type="text" name="user_name" id="user_name" value="<?php echo esc_attr($current_user->display_name); ?>" required /><br><br>

        <label for="user_email">Email Address:</label>
        <input type="email" name="user_email" id="user_email" value="<?php echo esc_attr($current_user->user_email); ?>" required /><br><br>

        <h3>Change Password</h3>
        
        <label for="user_password">New Password:</label>
        <input type="password" name="user_password" id="user_password" /><br><br>

        <label for="user_password_confirm">Confirm New Password:</label>
        <input type="password" name="user_password_confirm" id="user_password_confirm" /><br><br>

        <input type="submit" name="submit_profile_update" class="mq-button" value="Update Profile" />
    </form>

    <?php
    if (isset($_POST['submit_profile_update'])) {
        $user_name = sanitize_text_field($_POST['user_name']);
        $user_email = sanitize_email($_POST['user_email']);
        $new_password = isset($_POST['user_password']) ? sanitize_text_field($_POST['user_password']) : '';
        $password_confirm = isset($_POST['user_password_confirm']) ? sanitize_text_field($_POST['user_password_confirm']) : '';

        if ($user_name !== $current_user->display_name) {
            wp_update_user([
                'ID' => $current_user->ID,
                'display_name' => $user_name,
            ]);
        }

        if ($user_email !== $current_user->user_email) {
            wp_update_user([
                'ID' => $current_user->ID,
                'user_email' => $user_email,
            ]);
        }

        if (!empty($new_password) && $new_password === $password_confirm) {
            wp_set_password($new_password, $current_user->ID);
            echo '<p>Password updated successfully!</p>';
        } elseif (!empty($new_password) && $new_password !== $password_confirm) {
            echo '<p>Passwords do not match. Please try again.</p>';
        }

        echo '<p>Profile updated successfully!</p>';
    }

    return ob_get_clean();
}

?>
