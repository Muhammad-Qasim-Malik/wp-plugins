<?php


if ( ! defined( 'ABSPATH' ) ) exit;

// Start session for syncing state across page reloads
if (!session_id()) {
    session_start();
}

/**
 * 1. DATABASE HELPERS
 */
function bsync_get_wp_fields($sync_type) {
    global $wpdb;
    
    // 1. Determine which tables to look at
    if ($sync_type === 'users') {
        $main_table = $wpdb->users;
        $meta_table = $wpdb->usermeta;
    } else {
        // 'posts' and 'multi' both pull from the posts table
        $main_table = $wpdb->posts;
        $meta_table = $wpdb->postmeta;
    }
    $columns = $wpdb->get_col("SHOW COLUMNS FROM $main_table");

    $meta_keys = $wpdb->get_col("SELECT DISTINCT meta_key FROM $meta_table");

    $virtual_fields = [];
    if ($sync_type !== 'users') {
        $virtual_fields[] = 'featured_image_url'; 
        $virtual_fields[] = 'post_permalink';    
    }

    $all_available_keys = array_merge(
        (array)$columns, 
        (array)$meta_keys, 
        $virtual_fields
    );

    $all_available_keys = array_filter(array_unique($all_available_keys));
    sort($all_available_keys);

    return $all_available_keys;
}

function bsync_get_bd_schema($type) {
    $schemas = [
        'users' => [
            'first_name', 'last_name', 'email', 'password', 'subscription_id',
            'company', 'phone_number', 'address1', 'address2', 'city', 'zip_code', 
            'state_code', 'state_ln', 'country_code', 'country_ln',
            'website', 'twitter', 'youtube', 'facebook', 'linkedin', 'instagram', 
            'pinterest', 'blog', 'snapchat', 'whatsapp',
            'position', 'profession_id', 'experience', 'credentials', 'affiliation', 
            'awards', 'about_me', 'quote',
            'logo', 'profile_photo', 'cover_photo',
            'lat', 'lon', 'auto_geocode', 'search_description',
            'member_tags', 'services', 'featured', 'active', 'verified', 'nationwide',
            'facebook_id', 'google_id',
            'cv', 'rep_matters', 'listing_type',
            'modtime', 'signup_date', 'last_login',
            'send_email_notifications'
        ],
        'posts' => [
            'post_title', 'post_content', 'post_category', 'post_type',
            'post_image', 'original_image_url', 'image_imported', 'auto_image_import',
            'post_tags', 'post_filename',
            'data_type', 'data_id', 'user_id',
            'post_status', 'post_author',
            'post_live_date', 'post_start_date', 'post_expire_date', 
            'revision_timestamp', 'sticky_post', 'sticky_post_expiration_date',
            'post_clicks', 'recurring_type',
            'lat', 'lon', 'country_sn', 'state_sn', 'auto_geocode',
            'post_token', 'revision_count'
        ],
        'multi' => [
            'group_name', 'group_desc', 'group_filename', 'group_status',
            'data_type', 'data_id', 'user_id',
            'group_location', 'group_category', 'country_sn', 'state_sn',
            'post_image', 'post_image_title', 'post_image_description',
            'post_tags',
            'property_status', 'property_type', 'property_price', 
            'property_beds', 'property_baths', 'property_sqr_foot',
            'sticky_post', 'sticky_post_expiration_date',
            'lat', 'lon', 'auto_geocode',
            'group_token', 'group_order', 'revision_count', 'auto_image_import',
            'date_updated'
        ]
    ];
    return $schemas[$type] ?? $schemas['posts'];
}

function bsync_get_credentials() {
    return [
        'api_key' => get_option('bsync_api_key', ''),
        'domain' => get_option('bsync_domain', ''),
    ];
}

function bsync_save_credentials($api_key, $domain) {
    update_option('bsync_api_key', sanitize_text_field($api_key));
    update_option('bsync_domain', esc_url_raw($domain));
}

function bsync_get_default_ids() {
    return [
        'subscription_id' => get_option('bsync_default_subscription_id', ''),
        'post_data_type_id' => get_option('bsync_default_post_data_type_id', ''),
        'group_data_type_id' => get_option('bsync_default_group_data_type_id', ''),
        'user_id' => get_option('bsync_default_user_id', ''),
    ];
}

function bsync_save_default_ids($subscription_id, $post_data_type_id, $group_data_type_id, $user_id) {
    update_option('bsync_default_subscription_id', sanitize_text_field($subscription_id));
    update_option('bsync_default_post_data_type_id', sanitize_text_field($post_data_type_id));
    update_option('bsync_default_group_data_type_id', sanitize_text_field($group_data_type_id));
    update_option('bsync_default_user_id', sanitize_text_field($user_id));
}

/**
 * 3. SETTINGS PAGE
 */
function bsync_render_settings_page() {
    $credentials = bsync_get_credentials();
    $default_ids = bsync_get_default_ids();
    
    if(isset($_POST['save_settings']) && check_admin_referer('bsync_settings_nonce')) {
        bsync_save_credentials($_POST['api_key'], $_POST['domain']);
        bsync_save_default_ids(
            $_POST['subscription_id'], 
            $_POST['post_data_type_id'], 
            $_POST['group_data_type_id'], 
            $_POST['user_id']
        );
        echo '<div class="notice notice-success is-dismissible"><p><strong>✓ Settings saved successfully!</strong></p></div>';
    }
    ?>
    
    <div class="bsync-container">
        <div class="bsync-header">
            <h1>BrilliantSync Pro</h1>
            <a href="?page=brilliantsync" class="bsync-settings-link">← Back to Sync</a>
        </div>

        <div class="bsync-content">
            <div class="bsync-section-title">Configure Your Integration</div>
            <p class="bsync-section-desc">Set your API credentials and default IDs once. They'll be used for all future syncs.</p>

            <form method="post" class="bsync-settings-form">
                <?php wp_nonce_field('bsync_settings_nonce', 'bsync_settings_nonce'); ?>

                <h3 style="font-size: 16px; font-weight: 500; margin-top: 24px; margin-bottom: 16px; color: #23282d;">🔐 API Credentials</h3>
                
                <div class="bsync-form-group">
                    <label class="bsync-label">API Key <span class="bsync-required">*</span></label>
                    <input type="text" name="api_key" value="<?php echo esc_attr($credentials['api_key']); ?>" placeholder="Enter your Brilliant Directories API Key" required>
                    <p style="font-size: 12px; color: #666; margin-top: 6px;">Found in your Brilliant Directories admin panel under Settings → API</p>
                </div>

                <div class="bsync-form-group">
                    <label class="bsync-label">Directory URL <span class="bsync-required">*</span></label>
                    <input type="url" name="domain" value="<?php echo esc_attr($credentials['domain']); ?>" placeholder="https://yourdirectory.com" required>
                    <p style="font-size: 12px; color: #666; margin-top: 6px;">Your Brilliant Directories main domain URL</p>
                </div>

                <button type="button" class="bsync-button" onclick="bsync_test_settings_connection()">
                    🧪 Test API Connection
                </button>
                <div id="test-result-settings" style="margin-top: 12px;"></div>

                <h3 style="font-size: 16px; font-weight: 500; margin-top: 32px; margin-bottom: 16px; color: #23282d;">📌 Default IDs</h3>
                <p style="font-size: 13px; color: #666; margin-bottom: 20px;">These IDs will be automatically populated in all sync operations.</p>

                <div class="bsync-form-group">
                    <label class="bsync-label">Default Subscription ID (for User Syncs) <span class="bsync-required">*</span></label>
                    <input type="number" name="subscription_id" value="<?php echo esc_attr($default_ids['subscription_id']); ?>" placeholder="e.g., 1">
                    <p style="font-size: 12px; color: #666; margin-top: 6px;">BD Subscription ID - Used when syncing WordPress Users</p>
                </div>

                <div class="bsync-form-group">
                    <label class="bsync-label">Default User ID (for Posts/Groups) <span class="bsync-required">*</span></label>
                    <input type="number" name="user_id" value="<?php echo esc_attr($default_ids['user_id']); ?>" placeholder="e.g., 1">
                    <p style="font-size: 12px; color: #666; margin-top: 6px;">BD User/Member ID - Who will be the author</p>
                </div>

                <div class="bsync-form-group">
                    <label class="bsync-label">Default Data Type ID for Posts (Single Image) <span class="bsync-required">*</span></label>
                    <input type="number" name="post_data_type_id" value="<?php echo esc_attr($default_ids['post_data_type_id']); ?>" placeholder="e.g., 14">
                    <p style="font-size: 12px; color: #666; margin-top: 6px;">BD Data Type ID for single image posts (used with data_type: 20)</p>
                </div>

                <div class="bsync-form-group">
                    <label class="bsync-label">Default Data Type ID for Groups (Multi-Image) <span class="bsync-required">*</span></label>
                    <input type="number" name="group_data_type_id" value="<?php echo esc_attr($default_ids['group_data_type_id']); ?>" placeholder="e.g., 10">
                    <p style="font-size: 12px; color: #666; margin-top: 6px;">BD Data Type ID for multi-image groups (used with data_type: 4)</p>
                </div>

                <div class="bsync-buttons" style="margin-top: 32px;">
                    <a href="?page=brilliantsync" class="bsync-button">← Back to Sync</a>
                    <button type="submit" name="save_settings" class="bsync-button bsync-button-primary">💾 Save Settings</button>
                </div>
            </form>

            <div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #e5e5e5;">
                <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 16px; color: #23282d;">❓ How to Find Your IDs</h3>
                
                <div style="background: #f9f9f9; padding: 16px; border-radius: 3px; border-left: 4px solid #0073aa; margin-bottom: 16px;">
                    <strong style="display: block; margin-bottom: 8px;">Subscription ID (User Syncs)</strong>
                    <p style="margin: 0; font-size: 13px; color: #666;">BD Admin → Subscriptions → Find your subscription and note its ID</p>
                </div>

                <div style="background: #f9f9f9; padding: 16px; border-radius: 3px; border-left: 4px solid #0073aa; margin-bottom: 16px;">
                    <strong style="display: block; margin-bottom: 8px;">User ID (Posts/Groups Syncs)</strong>
                    <p style="margin: 0; font-size: 13px; color: #666;">BD Admin → Members → Find the user who will own posts/groups → Note their ID</p>
                </div>

                <div style="background: #f9f9f9; padding: 16px; border-radius: 3px; border-left: 4px solid #0073aa; margin-bottom: 16px;">
                    <strong style="display: block; margin-bottom: 8px;">Data Type ID for Posts (Single Image)</strong>
                    <p style="margin: 0; font-size: 13px; color: #666;">BD Admin → Content Types → Find your single-image post type → Note its ID (used with data_type: 20)</p>
                </div>

                <div style="background: #f9f9f9; padding: 16px; border-radius: 3px; border-left: 4px solid #0073aa;">
                    <strong style="display: block; margin-bottom: 8px;">Data Type ID for Groups (Multi-Image)</strong>
                    <p style="margin: 0; font-size: 13px; color: #666;">BD Admin → Content Types → Find your multi-image portfolio/group type → Note its ID (used with data_type: 4)</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function bsync_test_settings_connection() {
        const apiKey = jQuery('input[name="api_key"]').val();
        const domain = jQuery('input[name="domain"]').val();
        
        if(!apiKey || !domain) {
            document.getElementById('test-result-settings').innerHTML = '<div class="bsync-warning-box">⚠️ Please fill in API Key and Domain first</div>';
            return;
        }
        
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bsync_test_api',
                api_key: apiKey,
                domain: domain
            },
            success: function(response) {
                if(response.success) {
                    document.getElementById('test-result-settings').innerHTML = '<div class="bsync-success-box">✓ Connection successful! Your API credentials are valid.</div>';
                } else {
                    document.getElementById('test-result-settings').innerHTML = '<div class="bsync-warning-box">✗ Connection failed. Check your API Key and Domain.</div>';
                }
            },
            error: function() {
                document.getElementById('test-result-settings').innerHTML = '<div class="bsync-warning-box">✗ Connection test failed. Please try again.</div>';
            }
        });
    }
    </script>
    <?php
}

/**
 * 4. MAIN ADMIN PAGE
 */
function bsync_render_admin_page() {
    $step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
    // Get sync_type from POST (current form) or from previous value stored in session
    $sync_type = $_POST['sync_type'] ?? (isset($_SESSION['bsync_sync_type']) ? $_SESSION['bsync_sync_type'] : 'users');
    // Save to session to maintain across page refreshes
    $_SESSION['bsync_sync_type'] = $sync_type;
    
    $credentials = bsync_get_credentials();
    $default_ids = bsync_get_default_ids();
    ?>
    
    <div class="bsync-container">
        <div class="bsync-header">
            <h1>BrilliantSync Pro</h1>
            <a href="?page=brilliantsync-settings" class="bsync-settings-link">Settings</a>
        </div>

        <div class="bsync-progress">
            <?php for($i = 1; $i <= 5; $i++): ?>
                <div class="bsync-step <?php echo $step == $i ? 'active' : ''; ?> <?php echo $step > $i ? 'completed' : ''; ?>">
                    <div class="bsync-step-number"><?php echo $i; ?></div>
                    <span class="bsync-step-label">
                        <?php 
                        $labels = ['Select Type', 'Choose Records', 'Map Fields', 'API Setup', 'Sync Data'];
                        echo $labels[$i-1];
                        ?>
                    </span>
                </div>
            <?php endfor; ?>
        </div>

        <div class="bsync-content">
            <form method="post" action="?page=brilliantsync&step=<?php echo $step + 1; ?>" id="bsync-form" onsubmit="return bsync_collect_selected_ids()">
                <?php wp_nonce_field('bsync_form', 'bsync_nonce'); ?>
                
                <input type="hidden" name="sync_type" value="<?php echo esc_attr($sync_type); ?>">
                <div id="bsync-selected-ids-container"></div>
                
                <?php if(isset($_POST['selected_ids'])) foreach($_POST['selected_ids'] as $id) echo '<input type="hidden" name="selected_ids[]" value="'.esc_attr($id).'">'; ?>
                <?php if(isset($_POST['map'])) foreach($_POST['map'] as $bk => $wk) { if(!empty($wk)) echo '<input type="hidden" name="map['.esc_attr($bk).']" value="'.esc_attr($wk).'">'; } ?>
                <?php if(isset($_POST['custom'])) foreach($_POST['custom'] as $bk => $cv) { if(!empty($cv)) echo '<input type="hidden" name="custom['.esc_attr($bk).']" value="'.esc_attr($cv).'">'; } ?>
                <?php foreach(['bd_sub_id', 'bd_ptype', 'bd_auth'] as $f) if(isset($_POST[$f])) echo '<input type="hidden" name="'.esc_attr($f).'" value="'.esc_attr($_POST[$f]).'">';  ?>

                <!-- STEP 1 -->
                <?php if($step == 1): ?>
                    <div class="bsync-section-title">What are you migrating?</div>
                    <p class="bsync-section-desc">Choose the type of data you want to sync to Brilliant Directories</p>
                    
                    <div class="bsync-form-group">
                        <label class="bsync-label">Migration Type</label>
                        <select name="sync_type" id="sync_type">
                            <option value="users" <?php selected($sync_type, 'users'); ?>>WP Users to BD Members</option>
                            <option value="posts" <?php selected($sync_type, 'posts'); ?>>Single Image Posts</option>
                            <option value="multi" <?php selected($sync_type, 'multi'); ?>>Multi-Image Posts</option>
                        </select>
                    </div>

                <!-- STEP 2 -->
                <?php elseif($step == 2): ?>
                    <div class="bsync-section-title">Select Records</div>
                    <p class="bsync-section-desc">
                        <?php 
                        echo "Syncing: ";
                        if($sync_type === 'users') echo "<strong>WordPress Users</strong>";
                        elseif($sync_type === 'posts') echo "<strong>Single Photo Posts</strong>";
                        else echo "<strong>Multi-Photo Posts</strong>";
                        ?>
                        (Showing up to 500 items)
                    </p>
                    
                    <!-- Search & Pagination Controls -->
                    <div style="background: #fafafa; padding: 15px; border-radius: 3px; border: 1px solid #e5e5e5; margin-bottom: 15px;">
                        <div style="display: grid; grid-template-columns: 1fr auto; gap: 12px; margin-bottom: 12px;">
                            <input type="text" id="bsync-search-input" placeholder="Search items..." style="margin: 0; padding: 8px 12px;">
                            <button type="button" class="bsync-button" onclick="bsync_perform_search()" style="padding: 8px 16px; white-space: nowrap;">
                                Search
                            </button>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px;">
                            <div style="color: #666;">
                                <strong id="bsync-total-count">Loading...</strong> items found
                                <span id="bsync-page-range" style="margin-left: 12px; color: #0073aa;"></span>
                            </div>
                            <div>
                                <button type="button" class="bsync-button" onclick="bsync_previous_page()" id="bsync-prev-btn" style="padding: 6px 12px;">← Prev</button>
                                <span id="bsync-page-indicator" style="margin: 0 8px; font-weight: 500; display: inline-block; min-width: 60px; text-align: center;">Page 1</span>
                                <button type="button" class="bsync-button" onclick="bsync_next_page()" id="bsync-next-btn" style="padding: 6px 12px;">Next →</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bsync-select-all">
                        <label>
                            <input type="checkbox" id="bsync-select-all-checkbox">
                            <label for="bsync-select-all-checkbox" style="margin: 0; display: inline;">Select All Displayed Items</label>
                        </label>
                    </div>
                    
                    <div class="bsync-items-container" id="bsync-items-container">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th>
                                        <?php 
                                        if($sync_type === 'users') echo 'Name / Email';
                                        else echo 'Title / Info';
                                        ?>
                                    </th>
                                    <th style="width: 150px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="bsync-items-tbody">
                                <tr><td colspan="3" style="text-align:center; padding:30px; color:#999;">Loading items...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <script>
                    let bsync_current_page = 1;
                    let bsync_items_per_page = 20;
                    let bsync_all_items = [];
                    let bsync_total_items = 0;
                    let bsync_search_term = '';
                    const bsync_sync_type = '<?php echo $sync_type; ?>';
                    
                    jQuery(document).ready(function($) {
                        // Load initial items
                        bsync_load_items();
                        
                        // Select all checkbox
                        $('#bsync-select-all-checkbox').on('change', function() {
                            const isChecked = $(this).prop('checked');
                            $('.bsync-item-checkbox').prop('checked', isChecked);
                        });
                        
                        $(document).on('change', '.bsync-item-checkbox', function() {
                            const total = $('.bsync-item-checkbox').length;
                            const checked = $('.bsync-item-checkbox:checked').length;
                            $('#bsync-select-all-checkbox').prop('checked', total === checked && total > 0);
                        });
                        
                        // Allow Enter key in search
                        $('#bsync-search-input').on('keypress', function(e) {
                            if(e.which === 13) {
                                bsync_perform_search();
                                return false;
                            }
                        });
                    });
                    
                    function bsync_load_items() {
                        jQuery('#bsync-items-tbody').html('<tr><td colspan="3" style="text-align:center; padding:30px; color:#999;">Loading items...</td></tr>');
                        
                        jQuery.post(ajaxurl, {
                            action: 'bsync_load_items_paginated',
                            sync_type: bsync_sync_type,
                            search: bsync_search_term,
                            page: bsync_current_page
                        }, function(response) {
                            if(response.success) {
                                bsync_all_items = response.data.items;
                                bsync_total_items = response.data.total;
                                bsync_render_items();
                                bsync_update_pagination_info();
                            } else {
                                jQuery('#bsync-items-tbody').html('<tr><td colspan="3" style="text-align:center; padding:30px; color:#999;">Error loading items</td></tr>');
                            }
                        });
                    }
                    
                    function bsync_render_items() {
                        let html = '';
                        
                        if(bsync_all_items.length === 0) {
                            html = '<tr><td colspan="3" style="text-align:center; padding:30px; color:#999;">No items found</td></tr>';
                        } else {
                            bsync_all_items.forEach(function(item) {
                                let name = item.name || item.title || '(Untitled)';
                                let detail = item.detail || item.email || item.status || '';
                                
                                html += '<tr>' +
                                    '<td><input type="checkbox" name="selected_ids[]" value="' + item.id + '" class="bsync-item-checkbox"></td>' +
                                    '<td>' +
                                    '<div style="font-weight: 500; color: #23282d;">' + bsync_escape_html(name) + '</div>' +
                                    (detail ? '<div style="font-size: 12px; color: #666;">' + bsync_escape_html(detail) + '</div>' : '') +
                                    '</td>' +
                                    '<td style="font-size: 12px; color: #666;">' + (item.type_label || '') + '</td>' +
                                    '</tr>';
                            });
                        }
                        
                        jQuery('#bsync-items-tbody').html(html);
                        
                        // Re-attach checkbox event listeners
                        jQuery('.bsync-item-checkbox').on('change', function() {
                            const total = jQuery('.bsync-item-checkbox').length;
                            const checked = jQuery('.bsync-item-checkbox:checked').length;
                            jQuery('#bsync-select-all-checkbox').prop('checked', total === checked && total > 0);
                        });
                    }
                    
                    function bsync_update_pagination_info() {
                        const totalPages = Math.ceil(bsync_total_items / bsync_items_per_page);
                        const startItem = ((bsync_current_page - 1) * bsync_items_per_page) + 1;
                        const endItem = Math.min(bsync_current_page * bsync_items_per_page, bsync_total_items);
                        
                        jQuery('#bsync-total-count').text(bsync_total_items);
                        jQuery('#bsync-page-range').text(startItem + ' - ' + endItem);
                        jQuery('#bsync-page-indicator').text('Page ' + bsync_current_page + ' of ' + totalPages);
                        
                        jQuery('#bsync-prev-btn').prop('disabled', bsync_current_page === 1);
                        jQuery('#bsync-next-btn').prop('disabled', bsync_current_page >= totalPages);
                    }
                    
                    function bsync_perform_search() {
                        bsync_search_term = jQuery('#bsync-search-input').val();
                        bsync_current_page = 1;
                        bsync_load_items();
                    }
                    
                    function bsync_next_page() {
                        const totalPages = Math.ceil(bsync_total_items / bsync_items_per_page);
                        if(bsync_current_page < totalPages) {
                            bsync_current_page++;
                            bsync_load_items();
                            jQuery('#bsync-items-container').scrollIntoView({behavior: 'smooth'});
                        }
                    }
                    
                    function bsync_previous_page() {
                        if(bsync_current_page > 1) {
                            bsync_current_page--;
                            bsync_load_items();
                            jQuery('#bsync-items-container').scrollIntoView({behavior: 'smooth'});
                        }
                    }
                    
                    function bsync_escape_html(text) {
                        const map = {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;'
                        };
                        return text.replace(/[&<>"']/g, m => map[m]);
                    }
                    
                    window.bsync_collect_selected_ids = function() {
                        // Get current step from page parameter
                        const url = new URL(window.location);
                        const currentStep = parseInt(url.searchParams.get('step') || '1');
                        
                        // Only collect on Step 2
                        if(currentStep !== 2) {
                            return true; // Allow form submission on other steps
                        }
                        
                        // Collect all checked IDs from Step 2
                        const selectedIds = [];
                        jQuery('.bsync-item-checkbox:checked').each(function() {
                            selectedIds.push(jQuery(this).val());
                        });
                        
                        if(selectedIds.length === 0) {
                            alert('Please select at least one item to sync!');
                            return false; // Prevent form submission
                        }
                        
                        // Clear previous container
                        jQuery('#bsync-selected-ids-container').empty();
                        
                        // Add hidden inputs for each selected ID
                        selectedIds.forEach(function(id) {
                            if (!existingIds.includes(id)) {
                                jQuery('#bsync-selected-ids-container').append(
                                    '<input type="hidden" name="selected_ids[]" value="' + id + '">'
                                );
                            }
                        });

                        // Allow form submission
                        return true;
                    };
                    </script>

                <!-- STEP 3 -->
                <?php elseif($step == 3): ?>
                    <div class="bsync-section-title">Map Fields</div>
                    <p class="bsync-section-desc">Link Brilliant Directories fields to your WordPress data</p>
                    
                    <div class="bsync-info-box">
                        ℹ️ Default IDs from Settings will be used. <a href="?page=brilliantsync-settings" style="color: #0073aa; font-weight: 500;">Edit defaults</a> or override below.
                        <?php if($sync_type === 'posts'): ?>
                            <br><strong style="color: #0073aa;">Single Image Posts:</strong> Uses "Post Data Type ID" with data_type=20
                        <?php elseif($sync_type === 'multi'): ?>
                            <br><strong style="color: #0073aa;">Multi-Image Groups:</strong> Uses "Group Data Type ID" with data_type=4
                        <?php endif; ?>
                    </div>

                    <?php if($sync_type === 'users'): ?>
                        <div style="background: #fff3cd; border-left: 4px solid #dc3545; padding: 12px; border-radius: 3px; margin-bottom: 20px;">
                            <strong style="color: #dc3545; display: block; margin-bottom: 6px;">⚠️ Required Fields for User Sync</strong>
                            <p style="margin: 0; font-size: 12px; color: #333;">The following fields must be mapped or have custom values to sync users:</p>
                            <ul style="margin: 6px 0 0 0; font-size: 12px; color: #333; padding-left: 20px;">
                                <li><strong>Email</strong> - User email address (must be valid)</li>
                                <li><strong>Password</strong> - User password (will be hashed by BD)</li>
                                <li><strong>Subscription ID</strong> - BD subscription where user will be created</li>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- <div style="background: #fafafa; padding: 12px; border-radius: 3px; border: 1px solid #e5e5e5; margin-bottom: 20px;">
                        <p style="font-size: 12px; color: #666; margin: 0 0 12px 0; font-weight: 500;">Override Default IDs (Optional)</p>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                            <?php if($sync_type=='users'): ?>
                                <div>
                                    <label class="bsync-label" style="margin-bottom: 4px;">Subscription ID</label>
                                    <input type="number" name="bd_sub_id" value="<?php echo isset($_POST['bd_sub_id']) ? esc_attr($_POST['bd_sub_id']) : ''; ?>" placeholder="Default: <?php echo esc_attr($default_ids['subscription_id'] ?: 'Not set'); ?>">
                                </div>
                            <?php elseif($sync_type=='posts'): ?>
                                <div>
                                    <label class="bsync-label" style="margin-bottom: 4px;">Data Type ID <span style="color: #0073aa;">(Single Image Posts)</span></label>
                                    <input type="number" name="bd_ptype" value="<?php echo isset($_POST['bd_ptype']) ? esc_attr($_POST['bd_ptype']) : ''; ?>" placeholder="Default: <?php echo esc_attr($default_ids['post_data_type_id'] ?: 'Not set'); ?>">
                                    <p style="font-size: 11px; color: #666; margin-top: 4px;">For single-image posts (data_type: 20)</p>
                                </div>
                                <div>
                                    <label class="bsync-label" style="margin-bottom: 4px;">User ID</label>
                                    <input type="number" name="bd_auth" value="<?php echo isset($_POST['bd_auth']) ? esc_attr($_POST['bd_auth']) : ''; ?>" placeholder="Default: <?php echo esc_attr($default_ids['user_id'] ?: 'Not set'); ?>">
                                </div>
                            <?php else: ?>
                                <div>
                                    <label class="bsync-label" style="margin-bottom: 4px;">Data Type ID <span style="color: #0073aa;">(Multi-Image Groups)</span></label>
                                    <input type="number" name="bd_ptype" value="<?php echo isset($_POST['bd_ptype']) ? esc_attr($_POST['bd_ptype']) : ''; ?>" placeholder="Default: <?php echo esc_attr($default_ids['group_data_type_id'] ?: 'Not set'); ?>">
                                    <p style="font-size: 11px; color: #666; margin-top: 4px;">For multi-image groups (data_type: 4)</p>
                                </div>
                                <div>
                                    <label class="bsync-label" style="margin-bottom: 4px;">User ID</label>
                                    <input type="number" name="bd_auth" value="<?php echo isset($_POST['bd_auth']) ? esc_attr($_POST['bd_auth']) : ''; ?>" placeholder="Default: <?php echo esc_attr($default_ids['user_id'] ?: 'Not set'); ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div> -->

                    <table class="bsync-mapping-table">
                        <thead>
                            <tr>
                                <th>Brilliant Directories Field</th>
                                <th>WordPress Source Field</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $wf = bsync_get_wp_fields($sync_type);
                            $existing_map = $_POST['map'] ?? [];
                            $existing_custom = $_POST['custom'] ?? [];
                            
                            // Required fields for user syncs
                            $required_user_fields = ['subscription_id', 'email', 'password'];
                            
                            foreach(bsync_get_bd_schema($sync_type) as $f): 
                                $current_value = $existing_map[$f] ?? '';
                                $custom_value = $existing_custom[$f] ?? '';
                                $is_required = ($sync_type === 'users' && in_array($f, $required_user_fields));
                                ?>
                                <tr>
                                    <td>
                                        <div class="bsync-field-name">
                                            <?php echo esc_html($f); ?>
                                            <?php if(in_array($f, ['post_status', 'post_type', 'post_image'])) echo '<span class="bsync-field-badge">SPECIAL</span>'; ?>
                                            <?php if($is_required) echo '<span class="bsync-field-badge bsync-field-badge-required">REQUIRED</span>'; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="map[<?php echo esc_attr($f); ?>]" class="bsync-field-select" data-field="<?php echo esc_attr($f); ?>" <?php if($is_required) echo 'required'; ?>>
                                            <option value="">-- Skip This Field --</option>
                                            <option value="__custom__" <?php selected($current_value, '__custom__'); ?>> Custom Value</option>
                                            <?php foreach($wf as $w): ?>
                                                <option value="<?php echo esc_attr($w); ?>" <?php selected($current_value, $w); ?>><?php echo esc_html($w); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        
                                        <?php if($current_value === '__custom__'): ?>
                                            <div class="bsync-custom-value">
                                                <input type="text" name="custom[<?php echo esc_attr($f); ?>]" placeholder="Enter custom value" value="<?php echo esc_attr($custom_value); ?>" <?php if($is_required) echo 'required'; ?>>
                                            </div>
                                        <?php else: ?>
                                            <div class="bsync-custom-value" style="display:none;">
                                                <input type="text" name="custom[<?php echo esc_attr($f); ?>]" placeholder="Enter custom value" value="<?php echo esc_attr($custom_value); ?>">
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <script>
                    jQuery(document).ready(function($) {
                        $('.bsync-field-select').on('change', function() {
                            const $customDiv = $(this).closest('td').find('.bsync-custom-value');
                            if($(this).val() === '__custom__') {
                                $customDiv.show().find('input').focus();
                            } else {
                                $customDiv.hide();
                            }
                        });
                    });
                    </script>

                <!-- STEP 4 -->
                <?php elseif($step == 4): ?>
                    <div class="bsync-section-title">🔐 API Connection</div>
                    <p class="bsync-section-desc">Your API credentials are managed in Settings</p>
                    
                    <div class="bsync-info-box">
                        ℹ️ Update your API credentials <a href="?page=brilliantsync-settings" style="color: #0073aa; font-weight: 500;">in the Settings page</a>
                    </div>

                    <div style="background: #f9f9f9; padding: 16px; border-radius: 3px; border: 1px solid #ddd; margin-bottom: 20px;">
                        <strong style="display: block; font-size: 12px; color: #666; margin-bottom: 8px;">Current API Configuration:</strong>
                        <div style="font-size: 13px; color: #23282d;">
                            API Key: <code style="background: #fff; padding: 2px 6px; border-radius: 3px;"><?php echo esc_html($credentials['api_key'] ? substr($credentials['api_key'], 0, 10) . '...' : 'Not set'); ?></code><br>
                            Domain: <code style="background: #fff; padding: 2px 6px; border-radius: 3px;"><?php echo esc_html($credentials['domain'] ?: 'Not set'); ?></code>
                        </div>
                    </div>

                    <button type="button" class="bsync-button" onclick="bsync_test_connection()">
                        🧪 Test Connection
                    </button>
                    <div id="test-result" style="margin-top: 12px;"></div>

                    <script>
                    function bsync_test_connection() {
                        const apiKey = '<?php echo esc_js($credentials['api_key']); ?>';
                        const domain = '<?php echo esc_js($credentials['domain']); ?>';
                        
                        if(!apiKey || !domain) {
                            document.getElementById('test-result').innerHTML = '<div class="bsync-warning-box">⚠️ Please set API credentials in Settings first</div>';
                            return;
                        }
                        
                        jQuery.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'bsync_test_api',
                                api_key: apiKey,
                                domain: domain
                            },
                            success: function(response) {
                                if(response.success) {
                                    document.getElementById('test-result').innerHTML = '<div class="bsync-success-box">✓ Connection successful! API is reachable.</div>';
                                } else {
                                    document.getElementById('test-result').innerHTML = '<div class="bsync-warning-box">✗ Connection failed. Check your credentials in Settings.</div>';
                                }
                            }
                        });
                    }
                    </script>

                <!-- STEP 5 -->
                <?php elseif($step == 5): ?>
                    <div class="bsync-section-title">⚡ Synchronization In Progress</div>
                    <p class="bsync-section-desc">Your data is being synced to Brilliant Directories...</p>
                    
                    <div class="bsync-log" id="log">
                        <div class="bsync-log-line bsync-log-info">▶ Starting sync process...</div>
                    </div>

                    <script>
                    jQuery(document).ready(function($){
                        const ids = <?php echo json_encode($_POST['selected_ids'] ?? []); ?>;
                        console.log(ids);
                        let c = 0;
                        const logEl = $('#log');
                        
                        function log(msg, type = 'info') {
                            const classes = {
                                success: 'bsync-log-success',
                                error: 'bsync-log-error',
                                info: 'bsync-log-info'
                            };
                            logEl.append('<div class="bsync-log-line ' + classes[type] + '">' + msg + '</div>');
                            logEl.scrollTop(logEl[0].scrollHeight);
                        }
                        
                        function go() {
                            if(c >= ids.length) {
                                log('✓ SYNC COMPLETED!', 'success');
                                return;
                            }
                            
                            $.post(ajaxurl, {
                                action: 'bsync_run',
                                id: ids[c],
                                sync_type: '<?php echo $sync_type; ?>',
                                config: <?php echo json_encode($_POST); ?>
                            }, function (res) {

                                const index = '[' + (c + 1) + '/' + ids.length + ']';
                                const itemLabel = res.data.item;
                                console.log(res);
                                if (res.success) {
                                    log(
                                        `${index} ✓ ${itemLabel} — Successfully synced`,
                                        'success'
                                    );
                                } else {
                                    log(
                                        `${index} ✗ ${itemLabel} — ${res.data.message}`,
                                        'error'
                                    );
                                }

                                c++;
                                go();

                            }).fail(function () {

                                const index = '[' + (c + 1) + '/' + ids.length + ']';

                                log(
                                    `${index} ✗ ID #${ids[c]} — Request failed`,
                                    'error'
                                );

                                c++;
                                go();
                            });

                        }
                        
                        go();
                    });
                    </script>
                <?php endif; ?>

                <div class="bsync-buttons">
                    <?php if($step > 1 && $step < 5): ?>
                        <button type="button" class="bsync-button" onclick="window.history.back()">← Back</button>
                    <?php else: echo '<span></span>'; endif; ?>
                    
                    <?php if($step < 5): ?>
                        <button type="submit" class="bsync-button bsync-button-primary">Next Step →</button>
                    <?php else: ?>
                        <a href="?page=brilliantsync" class="bsync-button bsync-button-primary" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">↻ Start New Sync</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <?php
}

/**
 * 5. AJAX HANDLER FOR PAGINATED ITEMS
 */
add_action('wp_ajax_bsync_load_items_paginated', function(){
    $sync_type = sanitize_text_field($_POST['sync_type'] ?? 'users');
    $search = sanitize_text_field($_POST['search'] ?? '');
    $page = max(1, (int)$_POST['page'] ?? 1);
    $per_page = 20;
    $offset = ($page - 1) * $per_page;
    
    $items = [];
    $total = 0;
    
    if($sync_type === 'users') {
        // Get users with search
        $user_query = [
            'number' => $per_page,
            'offset' => $offset,
            'orderby' => 'display_name',
        ];
        
        if(!empty($search)) {
            $user_query['search'] = '*' . $search . '*';
        }
        
        $users = get_users($user_query);
        $total_query = get_users(['number' => -1, 'fields' => 'ID']);
        if(!empty($search)) {
            $total_query = new WP_User_Query(['search' => '*' . $search . '*', 'fields' => 'ID']);
            $total = $total_query->get_total();
        } else {
            $total = count($total_query);
        }
        
        foreach($users as $user) {
            $items[] = [
                'id' => $user->ID,
                'name' => $user->display_name,
                'email' => $user->user_email,
                'detail' => $user->user_email,
                'type_label' => $user->roles ? ucfirst($user->roles[0]) : 'User',
            ];
        }
    } else {
        // Get posts with search
        $post_type = 'post';
        $post_query = [
            'posts_per_page' => $per_page,
            'paged' => $page,
            'post_status' => 'any',
            'orderby' => 'title',
            'order' => 'ASC',
        ];
        
        if(!empty($search)) {
            $post_query['s'] = $search;
        }
        
        $posts = get_posts($post_query);
        
        // Get total count
        $count_query = [
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ];
        if(!empty($search)) {
            $count_query['s'] = $search;
        }
        $total_posts = count(get_posts($count_query));
        $total = $total_posts;
        
        foreach($posts as $post) {
            $status_label = '';
            switch($post->post_status) {
                case 'publish':
                    $status_label = 'Published';
                    break;
                case 'draft':
                    $status_label = 'Draft';
                    break;
                case 'pending':
                    $status_label = 'Pending';
                    break;
                default:
                    $status_label = ucfirst($post->post_status);
            }
            
            $items[] = [
                'id' => $post->ID,
                'name' => $post->post_title ?: '(Untitled)',
                'title' => $post->post_title ?: '(Untitled)',
                'detail' => 'ID: ' . $post->ID . ' | Date: ' . date('M d, Y', strtotime($post->post_date)),
                'status' => $post->post_status,
                'type_label' => $status_label,
            ];
        }
    }
    
    wp_send_json_success([
        'items' => $items,
        'total' => $total,
        'page' => $page,
    ]);
});

/**
 * 6. AJAX HANDLER FOR SYNCING
 */
add_action('wp_ajax_bsync_run', function(){
    $id = (int)$_POST['id'];
    $st = sanitize_text_field($_POST['sync_type']);
    $cfg = $_POST['config'] ?? [];
    
    $credentials = bsync_get_credentials();
    $api_key = $cfg['api_key'] ?? $credentials['api_key'];
    $domain = rtrim($cfg['domain'] ?? $credentials['domain'], '/');
    
    if(!$api_key || !$domain) {
        wp_send_json_error(['message' => 'Missing API credentials. Configure in Settings.']);
        return;
    }
    
    $wp_obj = ($st == 'users') ? get_userdata($id) : get_post($id);
    
    if(!$wp_obj) {
        wp_send_json_error(['message' => 'WordPress item not found']);
        return;
    }

    if ($st === 'users') {
        $item_label = $wp_obj->user_email ?? 'User ID: ' . $id;
    } else {
        $item_label = get_the_title($id) ?: 'Post ID: ' . $id;
    }
    
    $payload = [];
    $map = $cfg['map'] ?? [];
    $custom = $cfg['custom'] ?? [];
    
    // Build payload from mappings
    foreach($map as $bk => $wk) {
        // Skip empty mappings
        if(empty($wk)) {
            continue;
        }
        
        $val = '';
        
        // Get custom value if specified
        if($wk === '__custom__') {
            $val = isset($custom[$bk]) ? $custom[$bk] : '';
        } else {
            // Get from user/post meta or object properties
            if($st == 'users') {
                // Try user meta first
                $val = get_user_meta($id, $wk, true);
                // Fall back to user object property
                if(empty($val) && isset($wp_obj->$wk)) {
                    $val = $wp_obj->$wk;
                }
            } else {
                // Try post meta first
                $val = get_post_meta($id, $wk, true);
                // Fall back to post object property
                if(empty($val) && isset($wp_obj->$wk)) {
                    $val = $wp_obj->$wk;
                }
            }
            
            // Convert image attachments to URLs
            if($bk == 'post_image' && is_numeric($val)) {
                $val = wp_get_attachment_url($val);
            }
        }
        
        // Only add non-empty values to payload
        if(!empty($val)) {
            $payload[$bk] = $val;
        }

    }
    error_log(print_r($payload, true));
    
    $default_ids = bsync_get_default_ids();
    
    // Set up sync-type specific fields
    if($st == 'users') {
        // $payload['subscription_id'] = !empty($cfg['bd_sub_id']) ? $cfg['bd_sub_id'] : $default_ids['subscription_id'];
        
        // Validate required fields for users
        if(empty($payload['subscription_id'])) {
            wp_send_json_error(['message' => 'Subscription ID not set. Configure in Settings or Step 3.', 'item'    => $item_label]);
            return;
        }
        if(empty($payload['email'])) {
            wp_send_json_error(['message' => 'Email field not mapped or empty', 'item'    => $item_label]);
            return;
        }
        if(empty($payload['password'])) {
            wp_send_json_error(['message' => 'Password field not mapped or empty', 'item'    => $item_label]);
            return;
        }
        
        $endpoint = "$domain/api/v2/user/create";
    } elseif($st == 'posts') {
        // $payload['data_id'] = !empty($cfg['bd_ptype']) ? $cfg['bd_ptype'] : $default_ids['post_data_type_id'];
        // $payload['user_id'] = !empty($cfg['bd_auth']) ? $cfg['bd_auth'] : $default_ids['user_id'];
        $payload['data_type'] = 20;
        
        if(empty($payload['data_id'])) {
            wp_send_json_error(['message' => 'Post Data Type ID not set. Configure in Settings or Step 3.', 'item'    => $item_label]);
            return;
        }
        if(empty($payload['user_id'])) {
            wp_send_json_error(['message' => 'User ID not set. Configure in Settings or Step 3.', 'item'    => $item_label]);
            return;
        }
        
        $endpoint = "$domain/api/v2/data_posts/create";
    } else {
        // Multi-photo posts
        // $payload['data_id'] = !empty($cfg['bd_ptype']) ? $cfg['bd_ptype'] : $default_ids['group_data_type_id'];
        // $payload['user_id'] = !empty($cfg['bd_auth']) ? $cfg['bd_auth'] : $default_ids['user_id'];
        $payload['data_type'] = 4;
        
        if(empty($payload['data_id'])) {
            wp_send_json_error(['message' => 'Group Data Type ID not set. Configure in Settings or Step 3.', 'item'    => $item_label]);
            return;
        }
        if(empty($payload['user_id'])) {
            wp_send_json_error(['message' => 'User ID not set. Configure in Settings or Step 3.', 'item'    => $item_label]);
            return;
        }
        
        $endpoint = "$domain/api/v2/users_portfolio_groups/create";
    }
    
    // Make API request
    $response = wp_remote_post($endpoint, [
        'headers' => [
            'X-Api-Key' => $api_key,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ],
        'body' => $payload,
        'timeout' => 30
    ]);
    
    // Handle request errors
    if(is_wp_error($response)) {
        wp_send_json_error(['message' => 'API Error: ' . $response->get_error_message()]);
        return;
    }
    
    $status_code = wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    // Check for success
    if($status_code === 200 && isset($body['status']) && $body['status'] == 'success') {
        wp_send_json_success(['message' => 'Successfully synced', 'item'    => $item_label]);
    } else if($status_code === 200 && isset($body['success']) && $body['success'] === true) {
        // Alternative success format
        wp_send_json_success(['message' => 'Successfully synced', 'item'    => $item_label]);
    } else {
        // Extract error message
        $error_msg = 'API Error';
        if(isset($body['message'])) {
            $error_msg = $body['message'];
        } elseif(isset($body['error'])) {
            $error_msg = $body['error'];
        }
        wp_send_json_error(['message' => $error_msg . ' (Status: ' . $status_code . ')', 'item'    => $item_label]);
    }
});

add_action('wp_ajax_bsync_test_api', function(){
    $api_key = sanitize_text_field($_POST['api_key'] ?? '');
    $domain = esc_url_raw($_POST['domain'] ?? '');
    
    if(!$api_key || !$domain) {
        wp_send_json_error();
        return;
    }
    
    $response = wp_remote_get("$domain/api/v2/user/read", [
        'headers' => ['X-Api-Key' => $api_key],
        'timeout' => 10
    ]);
    
    if(is_wp_error($response)) {
        wp_send_json_error();
    } else {
        wp_send_json_success();
    }
});

/**
 * 7. REGISTER MENU
 */
add_action('admin_menu', function(){
    add_menu_page(
        'BrilliantSync Pro',
        'BrilliantSync Pro',
        'manage_options',
        'brilliantsync',
        'bsync_render_admin_page',
        'dashicons-database-view',
        75
    );
    
    add_submenu_page(
        'brilliantsync',
        'Settings',
        'Settings',
        'manage_options',
        'brilliantsync-settings',
        'bsync_render_settings_page'
    );
});

add_action('admin_init', function(){
    register_setting('bsync_credentials', 'bsync_api_key');
    register_setting('bsync_credentials', 'bsync_domain');
    register_setting('bsync_credentials', 'bsync_default_subscription_id');
    register_setting('bsync_credentials', 'bsync_default_post_data_type_id');
    register_setting('bsync_credentials', 'bsync_default_group_data_type_id');
    register_setting('bsync_credentials', 'bsync_default_user_id');
});