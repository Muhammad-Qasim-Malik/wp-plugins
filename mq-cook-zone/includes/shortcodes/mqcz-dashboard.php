<?php
function mqcz_dashboard_shortcode() {
    ?>
    <style>
        body{
            background: linear-gradient(145deg, #f7fafc, #e6e9ee) !important;
            
        }
        footer, .dropdown-menu {
            display: none !important;
        }
    </style>
    <?php

    $user = wp_get_current_user();

    if ( ! is_user_logged_in() ) {
        wp_redirect( home_url() );
        exit;
    } else {
        $roles = $user->roles;
        if ( !in_array( 'administrator', $roles ) && !in_array( 'chef_active', $roles ) && !in_array( 'chef_inactive', $roles ) ) {
            wp_redirect( site_url( '/profilegrid-profile/?uid=' . $user->ID ) );
            exit;
        }
    }


    $user_id = $user->ID;
    $error = '';

    if ( isset($_POST['mqcz_update_profile']) ) {
        $user_id = get_current_user_id();

        update_user_meta( $user_id, 'mqcz_facebook', sanitize_text_field($_POST['mqcz_facebook']) );
        update_user_meta( $user_id, 'mqcz_instagram', sanitize_text_field($_POST['mqcz_instagram']) );
        update_user_meta( $user_id, 'mqcz_youtube', sanitize_text_field($_POST['mqcz_youtube']) );

        update_user_meta( $user_id, 'first_name', sanitize_text_field($_POST['mqcz_first_name']) );
        update_user_meta( $user_id, 'last_name', sanitize_text_field($_POST['mqcz_last_name']) );
        update_user_meta( $user_id, 'mqcz_professional_summary', sanitize_textarea_field($_POST['mqcz_professional_summary']) );

        if ( is_email($_POST['mqcz_email']) ) {
            wp_update_user([ 
                'ID' => $user_id, 
                'user_email' => sanitize_email($_POST['mqcz_email']) 
            ]);
        }

        if ( ! empty($_FILES['mqcz_logo']['name']) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            $upload = wp_handle_upload( $_FILES['mqcz_logo'], ['test_form' => false] );
            if ( ! isset($upload['error']) ) {
                update_user_meta( $user_id, 'mqcz_logo', esc_url($upload['url']) );
            }
        }

        wp_redirect( site_url( '/dashboard?profile_updated=true' ) );
        exit;
    }


    if (isset($_POST['mqcz_change_password'])) {
        $user_id = get_current_user_id();

        // Get old, new, and confirm passwords from the form
        $old_password = $_POST['mqcz_old_password'];
        $new_password = $_POST['mqcz_new_password'];
        $confirm_password = $_POST['mqcz_confirm_password'];

        // Get the current user's data
        $user = get_user_by('id', $user_id);
        
        if (wp_check_password($old_password, $user->user_pass, $user_id)) {
            if ($new_password === $confirm_password) {
                wp_set_password($new_password, $user_id);

                wp_redirect(wp_logout_url(site_url('/login?password_changed=true')));
                exit;
            } else {
                $error = '<div class="mqcz-error">New password and confirmation password do not match.</div>';
            }
        } else {
            $error = '<div class="mqcz-error">Old password is incorrect.</div>';
        }
    }


    $wprm_recipe = get_posts([
        'post_type'   => 'wprm_recipe',
        'author'      => $user_id,
        'numberposts' => -1,
        'fields'      => 'ids',
    ]);

    $wprm_recipe_count = count($wprm_recipe);
    $wprm_recipe_likes = $wprm_recipe_views = 0;
    $first_recipe_status = '';

    if ($wprm_recipe_count === 1) {
        $first_recipe_id = $wprm_recipe[0];
        $first_recipe_status = get_post_status($first_recipe_id); 
    }

    foreach ( $wprm_recipe as $pid ) {
        $wprm_recipe_likes += (int) get_post_meta( $pid, 'mqcz_likes', true );
        $wprm_recipe_views += (int) get_post_meta( $pid, 'mqcz_views', true );
    }

    $level = 'New';
    if ( $wprm_recipe_count >= 5 && $wprm_recipe_likes >= 15 ) $level = 'Level 1';
    if ( $wprm_recipe_count >= 10 && $wprm_recipe_likes >= 30 ) $level = 'Level 2';
    if ( $wprm_recipe_count >= 20 && $wprm_recipe_likes >= 40 ) $level = 'Top Level';

    // Fetch user meta (profile info)
    $logo     = get_user_meta( $user_id, 'mqcz_logo', true );
    $facebook = get_user_meta( $user_id, 'mqcz_facebook', true );
    $youtube  = get_user_meta( $user_id, 'mqcz_youtube', true );
    $instagram = get_user_meta( $user_id, 'mqcz_instagram', true );

    ob_start();
    ?>

   

    <div class="mqcz-dashboard">
        <?php
            if (in_array($user->roles[0], ['chef_active'])) {
                echo '<div class="mqcz-success">✅ Your document has been approved by the admin.</div>';
            } elseif (in_array($user->roles[0], ['chef_inactive'])) {
                echo '<div class="mqcz-alert">⏳ Your document has not been approved by the admin yet.</div>';
            }

            if ($wprm_recipe_count === 1 && $first_recipe_status === 'publish') {
                echo '<div class="mqcz-success">✅ Your recipe has been approved by the admin.</div>';
            } elseif ($wprm_recipe_count === 1 && $first_recipe_status !== 'publish') {
                echo $first_recipe_approved;
                echo '<div class="mqcz-alert">⏳ Your recipe is awaiting approval from the admin.</div>';
            } elseif ($wprm_recipe_count === 0) {
                // echo '<div class="mqcz-warning">⚠️ You have no recipes yet.</div>';
            }

            echo $error;
        ?>

        <h2 style="font-family: 'Playfair Display', serif; font-size: 30px">
            Welcome, <?php echo esc_html( $user->display_name ); ?>!
        </h2>

        <div class="mqcz-tab-container">
            <div class="mqcz-tabs">
                <button data-tab="stats" class="active">Stats</button>
                <button data-tab="profile">Profile</button>
                <button data-tab="password">Password</button>
                <button data-tab="wprm_recipe">Recipes</button>
                <button data-tab="comments">Comments</button>
                <button data-tab="chat_posts">Chats</button> 
                <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>"
                    class="mqcz-tab-btn"
                    onclick="return confirm('Are you sure you want to log out?');">
                    Logout
                </a>

            </div>

            <div class="mqcz-tab-contents">
                <!-- Stats Tab -->
                <div class="mqcz-tab-content" id="stats" style="display:block;">
                    <div class="mqcz-cards">
                        <div class="mqcz-card">
                            <div class="mqcz-card-number"><?php echo $wprm_recipe_count; ?></div>
                            <div class="mqcz-card-label">Your Recipes</div>
                        </div>
                        <div class="mqcz-card">
                            <div class="mqcz-card-number"><?php echo $wprm_recipe_likes; ?></div>
                            <div class="mqcz-card-label">Total Likes</div>
                        </div>
                        <div class="mqcz-card">
                            <div class="mqcz-card-number"><?php echo $wprm_recipe_views; ?></div>
                            <div class="mqcz-card-label">Total Views</div>
                        </div>
                        <div class="mqcz-card" id="mqcz-show-level">
                            <div class="mqcz-card-number"><?php echo esc_html( $level ); ?></div>
                            <div class="mqcz-card-label">Your Level</div>
                        </div>
                    </div>
                    <div id="mqcz-level-info" style="display: none;">
                        <h3>Your Current Level: <?php echo esc_html( $level ); ?></h3>
                        <ul class="mqcz-level-list">
                            <li><strong>Level 1:</strong> Requires at least 5 recipes and 15 likes.</li>
                            <li><strong>Level 2:</strong> Requires at least 10 recipes and 30 likes.</li>
                            <li><strong>Top Level:</strong> Requires at least 20 recipes and 40 likes.</li>
                            <li><strong>New:</strong> You are just getting started!</li>
                        </ul>
                    </div>

                </div>

                <!-- Profile Tab -->
                <div class="mqcz-tab-content" id="profile" style="display:none;">
                    <div class="mqcz-profile-form">
                        <form method="post" enctype="multipart/form-data">
                            <div class="mqcz-form-group">
                                <label>Profile Image</label>
                                <?php if ( $logo ) : ?>
                                    <img src="<?php echo esc_url( $logo ); ?>" class="mqcz-profile-image">
                                <?php endif; ?>
                                <input type="file" name="mqcz_logo" accept="image/*">
                            </div>

                            <div class="mqcz-form-group">
                                <label>Email Address</label>
                                <input type="email" name="mqcz_email" value="<?php echo esc_attr( $user->user_email ); ?>" required>
                            </div>
                            
                            <!-- First Name Field -->
                            <div class="mqcz-form-group">
                                <label>First Name</label>
                                <input type="text" name="mqcz_first_name" value="<?php echo esc_attr( $first_name ); ?>" required>
                            </div>

                            <!-- Last Name Field -->
                            <div class="mqcz-form-group">
                                <label>Last Name</label>
                                <input type="text" name="mqcz_last_name" value="<?php echo esc_attr( $last_name ); ?>" required>
                            </div>

                            <!-- Professional Summary Field -->
                            <div class="mqcz-form-group">
                                <label>Professional Summary</label>
                                <textarea name="mqcz_professional_summary" rows="4" required><?php echo esc_textarea( $professional_summary ); ?></textarea>
                            </div>

                            <div class="mqcz-form-group">
                                <label>Facebook Profile</label>
                                <input type="url" name="mqcz_facebook" value="<?php echo esc_attr( $facebook ); ?>" placeholder="https://facebook.com/yourprofile">
                            </div>

                            <div class="mqcz-form-group">
                                <label>Instagram Profile</label>
                                <input type="url" name="mqcz_instagram" value="<?php echo esc_attr( $instagram ); ?>" placeholder="https://instagram.com/yourprofile">
                            </div>

                            <div class="mqcz-form-group">
                                <label>YouTube Channel</label>
                                <input type="url" name="mqcz_youtube" value="<?php echo esc_attr( $youtube ); ?>" placeholder="https://youtube.com/yourchannel">
                            </div>
                            
                            <button type="submit" name="mqcz_update_profile" class="mqcz-btn">Update Profile</button>
                        </form>
                    </div>
                </div>

                <!-- Password Tab -->
                <div class="mqcz-tab-content" id="password" style="display:none;">
                    <div class="mqcz-profile-form">
                        <form method="post">
                            <div class="mqcz-form-group">
                                <!-- Old Password -->
                                <label>Old Password</label>
                                    <div class="password-field" style="position: relative;">
                                        <input type="password" name="mqcz_old_password" required minlength="8" placeholder="Enter your old password">
                                        <div class="eye-icon">
                                            <i class="fas fa-eye" style="cursor: pointer; position: absolute; right: 10px; top: 8px; transform: translateY(50%);"></i>
                                        </div>
                                    </div>
                            </div>
                            
                            <div class="mqcz-form-group">
                                <!-- New Password -->
                                
                                    <label>New Password</label>
                                    <div class="password-field" style="position: relative;">
                                        <input type="password" name="mqcz_new_password" required minlength="8" placeholder="Enter your new password">
                                        <div class="eye-icon">
                                            <i class="fas fa-eye" style="cursor: pointer; position: absolute; right: 10px; top: 8px; transform: translateY(50%);"></i>
                                        </div>
                                    </div>
                            </div>

                            <div class="mqcz-form-group">
                                <!-- Confirm New Password -->
                                <label>Confirm New Password</label>
                                    <div class="password-field" style="position: relative;">
                                        <input type="password" name="mqcz_confirm_password" required minlength="8" placeholder="Confirm your new password">
                                        <div class="eye-icon">
                                            <i class="fas fa-eye" style="cursor: pointer; position: absolute; right: 10px; top: 8px; transform: translateY(50%);"></i>
                                        </div>
                                    </div>
                            </div>
                            
                            <button type="submit" name="mqcz_change_password" class="mqcz-btn">Change Password</button>
                        </form>
                    </div>
                </div>


                <!-- Recipes Tab -->
                <div class="mqcz-tab-content" id="wprm_recipe" style="display:none;">
                    <?php
                        $user_id = get_current_user_id();
                        if (!isset($user->roles[0]) || $user->roles[0] !== 'chef_inactive') {  
                    ?>
                    <div class="mqcz-section-header">
                        <h3>Your Recipes</h3>
                        <button id="add-new-recipe-btn" class="mqcz-btn">Add New Recipe</button>
                    </div>

                    <?php
                    $wprm_recipe = get_posts([
                        'post_type'   => 'wprm_recipe',
                        'author'      => $user_id,
                        'numberposts' => -1,  
                    ]);

                    if ( empty( $wprm_recipe ) ) : ?>
                        <div class="recipe-grid-content" style="text-align: center; padding: 60px 20px;">
                            <h4 style="color: #6c757d;">No recipes yet</h4>
                            <p style="color: #6c757d;">Start sharing your culinary creations with the community!</p>
                        </div>
                        <div id="recipe-submission-form" style="display:none;">
                            <?php echo do_shortcode('[wprm-recipe-submission]'); ?>
                        </div>
                    <?php else : ?>
                        <div class="mqcz-occasions-grid recipe-grid-content">
                            <?php 
                            foreach ( $wprm_recipe as $post ) :
                                setup_postdata( $post );
                                $featured_image = get_the_post_thumbnail_url( $post->ID, 'medium' );
                                if ( !$featured_image ) {
                                    $featured_image = site_url( '/wp-content/uploads/2025/05/Capture.jpg' );
                                }
                                ?>

                                <div class="mqcz-occasion-card" onclick="window.location.href='<?php echo get_permalink( $post->ID ); ?>'">
                                    <div class="mqcz-occasion-image-wrapper">
                                        <img src="<?php echo esc_url( $featured_image ); ?>" 
                                            alt="<?php echo esc_attr( $post->post_title ); ?>" 
                                            class="mqcz-occasion-image">
                                        <div class="mqcz-image-overlay">
                                            <div class="mqcz-image-overlay-content">
                                                <div class="mqcz-likes">
                                                    <i class="fa fa-thumbs-up"></i> <?php echo get_post_meta( $post->ID, 'mqcz_likes', true ); ?>
                                                </div>
                                                <div class="mqcz-views">
                                                    <i class="fa fa-eye"></i> <?php echo get_post_meta( $post->ID, 'mqcz_views', true ); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mqcz-occasion-title">
                                        <?php echo esc_html( $post->post_title ); ?>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        </div>
                        <!-- Recipe Submission Form (Initially hidden) -->
                        <div id="recipe-submission-form" style="display:none;">
                            <?php echo do_shortcode('[wprm-recipe-submission]'); ?>
                        </div>
                    <?php endif; 
                        wp_reset_postdata();
                        } else {
                            ?>
                            <div style="text-align: center; padding: 60px 20px;">
                                <h4 style="color: #ffc107;">⏳ Pending Approval</h4>
                                <p style="color: #6c757d;">You need admin approval for the uploaded document to publish any occasion.</p>
                            </div>
                            <?php
                        }
                    ?>
                    
                    

                </div>

                <!-- Comments Tab -->
                <div class="mqcz-tab-content" id="comments" style="display:none;">
                    <h3 style="margin-bottom: 30px;">Comments on Your Posts</h3>
                    <?php
                        // Fetch the posts authored by the current user
                        $wprm_recipe = get_posts([
                            'post_type'   => 'wprm_recipe',
                            'author'      => $user_id,
                            'numberposts' => -1,
                        ]);

                        if (empty($wprm_recipe)) : ?>
                            <div style="text-align: center; padding: 60px 20px;">
                                <h4 style="color: #6c757d;">No posts to show comments</h4>
                                <p style="color: #6c757d;">Create some recipes to see comments here!</p>
                            </div>
                        <?php else : ?>
                            <div class="mqcz-accordion">
                                <?php
                                foreach ($wprm_recipe as $post) :
                                    setup_postdata($post);  // This sets the global $post object to the current post
                                    $comments = get_comments(array('post_id' => $post->ID));
                                    $comment_count = count($comments);
                                    ?>
                                    <div class="mqcz-accordion-item">
                                        <div class="mqcz-accordion-header" onclick="toggleAccordion(this)">
                                            <span><?php echo esc_html(get_the_title($post->ID)); ?> (<?php echo $comment_count; ?> comments)</span>
                                            <span class="mqcz-accordion-arrow">▼</span>
                                        </div>
                                        <div class="mqcz-accordion-content">
                                            <?php if ($comments) : ?>
                                                <table class="mqcz-comments-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Author</th>
                                                            <th>Comment</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($comments as $comment) : ?>
                                                            <tr>
                                                                <td><strong><?php echo esc_html($comment->comment_author); ?></strong></td>
                                                                <td><?php echo esc_html($comment->comment_content); ?></td>
                                                                <td><?php echo date('M j, Y', strtotime($comment->comment_date)); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php else : ?>
                                                <p style="color: #6c757d; text-align: center; padding: 20px;">No comments yet on this post.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php
                            wp_reset_postdata();  // Reset the global $post object to the original post
                        endif;
                    ?>
                </div>


                <!-- Chats Tab -->
                <div class="mqcz-tab-content" id="chat_posts" style="display:none;">
                    <h3 style="margin-bottom: 30px;">Chats</h3>
                    <?php
                    // Ensure the current user is viewing their own conversations
                    $user_id = get_current_user_id(); // Get the logged-in user ID

                    if( (int) $user_id === Better_Messages()->functions->get_current_user_id() ) {
                        echo '<div id="bm-pg-messages" class="pm-dbfl pg-profile-tab-content bm-pg-messages-tab" style="display: block">';
                        echo Better_Messages()->functions->get_page();
                        ?>
                        <script type="text/javascript">
                            var button = document.getElementById('bm-pg-messages-link');

                            button.addEventListener('click', function(){
                                let clickToOpen = document.querySelector('#bm-pg-messages .bp-messages-mobile-tap');
                                if( clickToOpen ) clickToOpen.click();
                            });

                            document.addEventListener('better-messages-autoscroll', function(){
                                var button = document.getElementById('bm-pg-messages-link');
                                if( button ) button.click();
                            });
                        </script>
                        <?php
                        echo '</div>';
                    }
                    ?>
                </div>

            </div>
        </div>

    </div>

    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.mqcz-tabs button[data-tab]');
            const tabContents = document.querySelectorAll('.mqcz-tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.style.display = 'none';
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Show selected tab content
                    document.getElementById(tabId).style.display = 'block';
                });
            });
        });

        // Accordion functionality
        function toggleAccordion(element) {
            const content = element.nextElementSibling;
            const arrow = element.querySelector('.mqcz-accordion-arrow');
            
            if (content.classList.contains('active')) {
                content.classList.remove('active');
                arrow.textContent = '▼';
            } else {
                // Close all accordions
                document.querySelectorAll('.mqcz-accordion-content').forEach(acc => {
                    acc.classList.remove('active');
                });
                document.querySelectorAll('.mqcz-accordion-arrow').forEach(arr => {
                    arr.textContent = '▼';
                });
                
                // Open clicked accordion
                content.classList.add('active');
                arrow.textContent = '▲';
            }
        }
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode( 'mqcz_dashboard', 'mqcz_dashboard_shortcode' );
?>