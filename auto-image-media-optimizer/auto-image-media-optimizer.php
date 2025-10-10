<?php
/*
Plugin Name: Auto Image & Media Optimizer
Description: Automatically optimizes images, converts to WebP, and replaces original PNG/JPEG with WebP (same filename, preserves attachment ID). Shows WebP in admin.
Version: 6.8
Author: Your Name
License: GPL2
*/

define('AIMO_VERSION', '6.8');

register_activation_hook(__FILE__, 'aimo_activate');
register_deactivation_hook(__FILE__, 'aimo_deactivate');

function aimo_activate() {
    if (!wp_next_scheduled('aimo_optimize_cron_hook')) {
        wp_schedule_event(time(), 'hourly', 'aimo_optimize_cron_hook');
    }
    if (!wp_next_scheduled('aimo_start_optimization')) {
        wp_schedule_single_event(time() + 5, 'aimo_start_optimization');
    }
}

function aimo_deactivate() {
    wp_clear_scheduled_hook('aimo_optimize_cron_hook');
    wp_clear_scheduled_hook('aimo_start_optimization');
}

add_action('aimo_optimize_cron_hook', 'aimo_batch_process');
add_action('aimo_start_optimization', 'aimo_batch_process');

// Auto-optimize on upload (convert to WebP and replace)
add_filter('wp_generate_attachment_metadata', function($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    $file = wp_normalize_path($file);
    if ($file && file_exists($file) && is_writable($file)) {
        aimo_compress_image($file, $attachment_id);
        update_post_meta($attachment_id, '_aimo_optimized', time());
        // error_log("AIMO: Auto-optimized and converted to WebP for ID $attachment_id");
    } else {
        error_log("AIMO: Failed to auto-optimize ID $attachment_id - File: $file, Exists: " . (file_exists($file) ? 'Yes' : 'No') . ", Writable: " . (is_writable($file) ? 'Yes' : 'No'));
    }
    return $metadata;
}, 10, 2);

// AJAX endpoints
add_action('wp_ajax_aimo_get_images', 'aimo_get_images_ajax');
add_action('wp_ajax_aimo_optimize_image', 'aimo_optimize_image_ajax');
add_action('wp_ajax_aimo_optimize_all', 'aimo_optimize_all_ajax');
add_action('wp_ajax_aimo_debug_webp', 'aimo_debug_webp_ajax');

function aimo_get_images_ajax() {
    check_ajax_referer('aimo_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }

    $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;
    $per_page = 20;

    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => $per_page,
        'paged' => $paged,
        'post_status' => 'inherit',
        'post_mime_type' => array('image/jpeg', 'image/png', 'image/webp')
    ));

    $images = array();
    foreach ($attachments as $attachment) {
        $file = get_attached_file($attachment->ID);
        $file = wp_normalize_path($file);
        $mime_type = get_post_mime_type($attachment->ID);

        if ($file && file_exists($file)) {
            clearstatcache(true, $file);
            $size = filesize($file);
            $is_optimized = get_post_meta($attachment->ID, '_aimo_optimized', true);
            $thumb = wp_get_attachment_image_url($attachment->ID, 'thumbnail') ?: '';

            $images[] = array(
                'id' => $attachment->ID,
                'name' => basename($file),
                'size' => $size,
                'size_display' => size_format($size, 2),
                'mime' => $mime_type,
                'original_mime' => $mime_type, // No separate WebP file
                'optimized' => $is_optimized ? true : false,
                'has_webp' => ($mime_type === 'image/webp') ? true : false,
                'url' => wp_get_attachment_url($attachment->ID),
                'thumb' => $thumb
            );
        } else {
            // error_log("AIMO: File missing for ID $attachment->ID: $file");
        }
    }

    wp_send_json_success([
        'images' => $images,
        'total' => count($images),
        'max_pages' => ceil(wp_count_posts('attachment')->inherit / $per_page)
    ]);
}

function aimo_optimize_image_ajax() {
    check_ajax_referer('aimo_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }

    if (!extension_loaded('gd')) {
        // error_log("AIMO AJAX: GD Library not installed");
        wp_send_json_error(['message' => 'GD Library not installed on server']);
    }

    $image_id = intval($_POST['image_id']);
    $file = get_attached_file($image_id);
    $file = wp_normalize_path($file);

    if (!$file || !file_exists($file) || !is_writable($file)) {
        // error_log("AIMO AJAX: File not found or not writable for ID $image_id: $file");
        wp_send_json_error(['message' => 'File not found or not writable']);
    }

    // error_log("AIMO AJAX: Starting optimization of file: $file");

    @ini_set('memory_limit', '512M');
    @set_time_limit(120);

    clearstatcache(true, $file);
    $before_size = filesize($file);

    $result = aimo_compress_image($file, $image_id);

    if ($result) {
        clearstatcache(true, $file);
        $after_size = filesize($file);

        // error_log("AIMO AJAX: Before: $before_size | After: $after_size");

        update_post_meta($image_id, '_aimo_optimized', time());

        wp_send_json_success([
            'before' => size_format($before_size, 2),
            'after' => size_format($after_size, 2),
            'saved' => size_format($before_size - $after_size, 2),
            'savings_percent' => $before_size > 0 ? round((($before_size - $after_size) / $before_size) * 100, 2) : 0
        ]);
    } else {
        // error_log("AIMO AJAX: Optimization failed for ID $image_id");
        wp_send_json_error(['message' => 'Could not optimize image. Check debug log for details.']);
    }
}

function aimo_optimize_all_ajax() {
    check_ajax_referer('aimo_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }

    if (!extension_loaded('gd')) {
        wp_send_json_error(['message' => 'GD Library not installed']);
    }

    $image_ids = isset($_POST['image_ids']) ? array_map('intval', $_POST['image_ids']) : array();
    if (empty($image_ids)) {
        wp_send_json_error(['message' => 'No images selected']);
    }

    @ini_set('memory_limit', '512M');
    @set_time_limit(120);

    $optimized = 0;
    $total_saved = 0;
    $failed = 0;

    foreach ($image_ids as $image_id) {
        $file = get_attached_file($image_id);
        $file = wp_normalize_path($file);

        if ($file && file_exists($file) && is_writable($file)) {
            clearstatcache(true, $file);
            $before_size = filesize($file);

            if (aimo_compress_image($file, $image_id)) {
                clearstatcache(true, $file);
                $after_size = filesize($file);
                $saved = $before_size - $after_size;

                if ($saved >= 0) {
                    $total_saved += $saved;
                    $optimized++;
                    update_post_meta($image_id, '_aimo_optimized', time());
                } else {
                    $failed++;
                    // error_log("AIMO AJAX: Optimized file larger for ID $image_id");
                }
            } else {
                $failed++;
                // error_log("AIMO AJAX: Optimization failed for ID $image_id");
            }
        } else {
            $failed++;
            // error_log("AIMO AJAX: File not found or not writable for ID $image_id: $file");
        }
    }

    // error_log("AIMO: Batch optimization completed. Optimized: $optimized | Failed: $failed | Total saved: $total_saved bytes");

    wp_send_json_success([
        'optimized' => $optimized,
        'failed' => $failed,
        'total_saved' => size_format($total_saved, 2)
    ]);
}

function aimo_debug_webp_ajax() {
    check_ajax_referer('aimo_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }

    $image_id = intval($_POST['image_id']);
    $file = get_attached_file($image_id);
    $file = wp_normalize_path($file);
    $mime_type = get_post_mime_type($image_id);

    $debug_info = [
        'image_id' => $image_id,
        'file' => $file,
        'file_exists' => file_exists($file) ? 'Yes' : 'No',
        'file_writable' => is_writable($file) ? 'Yes' : 'No',
        'file_size' => file_exists($file) ? filesize($file) : 0,
        'mime_type' => $mime_type,
        'uploads_dir' => wp_normalize_path(dirname($file)),
        'uploads_writable' => is_writable(dirname($file)) ? 'Yes' : 'No',
        'gd_loaded' => extension_loaded('gd') ? 'Yes' : 'No',
        'webp_support' => function_exists('imagewebp') ? 'Yes' : 'No'
    ];

    // error_log("AIMO Debug WebP: " . print_r($debug_info, true));

    if ($file && file_exists($file) && is_writable($file) && is_writable(dirname($file)) && function_exists('imagewebp') && $mime_type !== 'image/webp') {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $webp_quality = max(1, min(100, (int) get_option('aimo_webp_quality', 60)));
        $result = aimo_generate_webp($file, $webp_quality, $ext, $image_id);
        $debug_info['webp_replaced'] = $result ? 'Yes' : 'No';
        $debug_info['new_file_size'] = file_exists($file) ? filesize($file) : 0;
        $debug_info['new_mime_type'] = get_post_mime_type($image_id);
    }

    wp_send_json_success($debug_info);
}

function aimo_compress_image($file_path, $attachment_id) {
    $file_path = wp_normalize_path($file_path);
    if (!file_exists($file_path) || !is_writable($file_path)) {
        // error_log("AIMO: File not found or not writable: $file_path");
        return false;
    }

    if (!extension_loaded('gd')) {
        // error_log("AIMO: GD Library not loaded");
        return false;
    }

    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $jpeg_quality = (int) get_option('aimo_jpeg_quality', 65);
    $png_quality = (int) get_option('aimo_png_quality', 6);
    $webp_quality = (int) get_option('aimo_webp_quality', 60);

    $jpeg_quality = max(1, min(100, $jpeg_quality));
    $png_quality = max(0, min(9, $png_quality));
    $webp_quality = max(1, min(100, $webp_quality));

    $compressed = false;

    try {
        $temp_path = $file_path . '.aimo.tmp';

        if ($ext === 'jpg' || $ext === 'jpeg') {
            if (function_exists('imagecreatefromjpeg')) {
                $image = @imagecreatefromjpeg($file_path);
                if ($image !== false) {
                    imageinterlace($image, true);
                    $result = @imagejpeg($image, $temp_path, $jpeg_quality);
                    imagedestroy($image);

                    if ($result && file_exists($temp_path)) {
                        if (@copy($temp_path, $file_path)) {
                            @unlink($temp_path);
                            // error_log("AIMO: JPEG optimized successfully: $file_path");
                            $compressed = true;
                        }
                        @unlink($temp_path);
                    }
                } else {
                    // error_log("AIMO: Failed to load JPEG from $file_path");
                }
            }
        } elseif ($ext === 'png') {
            if (function_exists('imagecreatefrompng')) {
                $image = @imagecreatefrompng($file_path);
                if ($image !== false) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    $result = @imagepng($image, $temp_path, $png_quality, PNG_ALL_FILTERS);
                    imagedestroy($image);

                    if ($result && file_exists($temp_path)) {
                        if (@copy($temp_path, $file_path)) {
                            @unlink($temp_path);
                            // error_log("AIMO: PNG optimized successfully: $file_path");
                            $compressed = true;
                        }
                        @unlink($temp_path);
                    }
                } else {
                    // error_log("AIMO: Failed to load PNG from $file_path");
                }
            }
        } elseif ($ext === 'webp') {
            if (function_exists('imagecreatefromwebp')) {
                $image = @imagecreatefromwebp($file_path);
                if ($image !== false) {
                    $result = @imagewebp($image, $temp_path, $webp_quality);
                    imagedestroy($image);

                    if ($result && file_exists($temp_path)) {
                        if (@copy($temp_path, $file_path)) {
                            @unlink($temp_path);
                            // error_log("AIMO: WebP optimized successfully: $file_path");
                            $compressed = true;
                        }
                        @unlink($temp_path);
                    }
                } else {
                    // error_log("AIMO: Failed to load WebP from $file_path");
                }
            }
        } else {
            // error_log("AIMO: Unsupported format: $ext");
        }

        // Convert to WebP and replace original for JPEG/PNG if enabled
        if (($ext === 'jpg' || $ext === 'jpeg' || $ext === 'png') && get_option('aimo_enable_webp', 1) && function_exists('imagewebp')) {
            $compressed = aimo_generate_webp($file_path, $webp_quality, $ext, $attachment_id) || $compressed;
        }

        return $compressed;

    } catch (Exception $e) {
        // error_log("AIMO Exception in compressing ID $attachment_id: " . $e->getMessage());
        return false;
    }
}

function aimo_generate_webp($source_path, $quality, $type, $attachment_id) {
    $source_path = wp_normalize_path($source_path);
    $uploads_dir = wp_normalize_path(dirname($source_path));
    if (!is_writable($uploads_dir)) {
        // error_log("AIMO: Uploads directory not writable: $uploads_dir");
        return false;
    }

    if (!function_exists('imagewebp')) {
        // error_log("AIMO: WebP support not available in GD for $source_path");
        return false;
    }

    @ini_set('memory_limit', '512M');
    @set_time_limit(120);

    $image = false;
    if ($type === 'jpeg' || $type === 'jpg') {
        $image = @imagecreatefromjpeg($source_path);
        if (!$image) {
            // error_log("AIMO: Failed to create JPEG resource for $source_path");
            return false;
        }
    } elseif ($type === 'png') {
        $image = @imagecreatefrompng($source_path);
        if ($image) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        } else {
            // error_log("AIMO: Failed to create PNG resource for $source_path");
            return false;
        }
    }

    if ($image) {
        // Strip metadata for PNG by converting to JPEG temporarily
        $temp_jpeg = $source_path . '.temp.jpg';
        if ($type === 'png') {
            imagejpeg($image, $temp_jpeg, 100);
            imagedestroy($image);
            $image = imagecreatefromjpeg($temp_jpeg);
            @unlink($temp_jpeg);
            if (!$image) {
                // error_log("AIMO: Failed to create temp JPEG for $source_path");
                return false;
            }
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        // Generate WebP to a temporary file
        $temp_webp = $source_path . '.temp.webp';
        $result = @imagewebp($image, $temp_webp, $quality);
        imagedestroy($image);

        if ($result && file_exists($temp_webp)) {
            clearstatcache(true, $source_path);
            $orig_size = file_exists($source_path) ? filesize($source_path) : 0;
            $webp_size = filesize($temp_webp);
            $savings = $orig_size - $webp_size;
            $savings_percent = $orig_size > 0 ? round(($savings / $orig_size) * 100, 2) : 0;

            // Replace original file with WebP
            if (@unlink($source_path) && @rename($temp_webp, $source_path)) {
                // error_log("AIMO: Replaced $source_path with WebP | Size: $webp_size bytes | Savings: $savings bytes ($savings_percent%)");
                
                // Update WordPress database
                $relative_path = str_replace(wp_normalize_path(wp_upload_dir()['basedir']), '', $source_path);
                $relative_path = ltrim($relative_path, '/\\');
                update_post_meta($attachment_id, '_wp_attached_file', $relative_path);
                wp_update_post(array(
                    'ID' => $attachment_id,
                    'post_mime_type' => 'image/webp'
                ));
                delete_post_meta($attachment_id, '_aimo_webp_path'); // No separate WebP file

                return true;
            } else {
                @unlink($temp_webp);
                error_log("AIMO: Failed to replace $source_path with WebP");
                return false;
            }
        } else {
            @unlink($temp_webp);
            // error_log("AIMO: Failed to generate WebP for $source_path (imagewebp failed)");
            return false;
        }
    }

    // error_log("AIMO: No image resource created for $source_path");
    return false;
}

function aimo_batch_process() {
    @ini_set('memory_limit', '512M');
    @set_time_limit(120);

    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => 5,
        'post_status' => 'inherit',
        'post_mime_type' => array('image/jpeg', 'image/png', 'image/webp'),
        'meta_query' => array(
            array(
                'key' => '_aimo_optimized',
                'compare' => 'NOT EXISTS'
            )
        )
    ));

    foreach ($attachments as $attachment) {
        $file = get_attached_file($attachment->ID);
        $file = wp_normalize_path($file);
        if ($file && file_exists($file) && is_writable($file)) {
            if (aimo_compress_image($file, $attachment->ID)) {
                update_post_meta($attachment->ID, '_aimo_optimized', time());
            }
        } else {
            // error_log("AIMO Batch: File not found or not writable for ID $attachment->ID: $file");
        }
    }
}

// Admin menu
add_action('admin_menu', function() {
    add_menu_page(
        'Image Optimizer',
        'Image Optimizer',
        'manage_options',
        'aimo-settings',
        'aimo_render_admin_page',
        'dashicons-images-alt2',
        30
    );
});

// Register settings
add_action('admin_init', function() {
    register_setting('aimo_settings_group', 'aimo_jpeg_quality', array(
        'type' => 'integer',
        'sanitize_callback' => function($value) {
            return max(1, min(100, absint($value)));
        },
        'default' => 65
    ));

    register_setting('aimo_settings_group', 'aimo_png_quality', array(
        'type' => 'integer',
        'sanitize_callback' => function($value) {
            return max(0, min(9, absint($value)));
        },
        'default' => 6
    ));

    register_setting('aimo_settings_group', 'aimo_webp_quality', array(
        'type' => 'integer',
        'sanitize_callback' => function($value) {
            return max(1, min(100, absint($value)));
        },
        'default' => 60
    ));

    register_setting('aimo_settings_group', 'aimo_enable_webp', array(
        'type' => 'boolean',
        'sanitize_callback' => function($value) {
            return $value ? 1 : 0;
        },
        'default' => 1
    ));

    add_settings_section('aimo_section', 'Compression Settings', function() {
        echo '<p>Configure compression levels for images. Converts JPEG/PNG to WebP and replaces original file (preserves attachment ID). Supported on Android 4.0+ and iOS 14+.</p>';
    }, 'aimo-settings');

    add_settings_field('aimo_jpeg_quality', 'JPEG Quality', function() {
        $value = get_option('aimo_jpeg_quality', 65);
        echo "<input type='number' name='aimo_jpeg_quality' value='" . esc_attr($value) . "' min='1' max='100' style='width:100px;' /> (1-100)";
        echo "<p style='margin: 5px 0; font-size: 12px;'>Lower = smaller files, lower quality. Recommended: 60-75</p>";
    }, 'aimo-settings', 'aimo_section');

    add_settings_field('aimo_png_quality', 'PNG Compression', function() {
        $value = get_option('aimo_png_quality', 6);
        echo "<input type='number' name='aimo_png_quality' value='" . esc_attr($value) . "' min='0' max='9' style='width:100px;' /> (0-9)";
        echo "<p style='margin: 5px 0; font-size: 12px;'>0 = no compression, 9 = maximum compression</p>";
    }, 'aimo-settings', 'aimo_section');

    add_settings_field('aimo_webp_quality', 'WebP Quality', function() {
        $value = get_option('aimo_webp_quality', 60);
        echo "<input type='number' name='aimo_webp_quality' value='" . esc_attr($value) . "' min='1' max='100' style='width:100px;' /> (1-100)";
        echo "<p style='margin: 5px 0; font-size: 12px;'>Lower = smaller files, lower quality. Recommended: 50-60 (saves 25-34% vs JPEG/PNG)</p>";
    }, 'aimo-settings', 'aimo_section');

    add_settings_field('aimo_enable_webp', 'Enable WebP Conversion', function() {
        $value = get_option('aimo_enable_webp', 1);
        echo "<input type='checkbox' name='aimo_enable_webp' value='1' " . checked($value, 1, false) . " />";
        echo "<label>Convert JPEG/PNG to WebP and replace original file (preserves attachment ID)</label>";
    }, 'aimo-settings', 'aimo_section');
});

function aimo_render_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    $gd_status = extension_loaded('gd') ? '<span style="color: green;">✓ Installed</span>' : '<span style="color: red;">✗ Not Installed</span>';
    $webp_status = function_exists('imagewebp') ? '<span style="color: green;">✓ Supported (Mobile: Android 4.0+, iOS 14+)</span>' : '<span style="color: red;">✗ Not Supported</span>';
    ?>
    <div style="max-width: 1200px; margin: 20px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <h1>Auto Image & Media Optimizer (WebP-Replaced)</h1>
        <p>Optimizes images, converts to WebP, and replaces original JPEG/PNG with WebP (same filename, preserves attachment ID).</p>

        <!-- Settings -->
        <div style="background: #fff; border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; border-radius: 5px;">
            <h2>Settings</h2>
            <form method="post" action="options.php">
                <?php settings_fields('aimo_settings_group'); do_settings_sections('aimo-settings'); submit_button(); ?>
            </form>
        </div>

        <!-- Images List -->
        <div style="background: #fff; border: 1px solid #ccc; padding: 20px; border-radius: 5px;">
            <h2>Optimize Images</h2>
            <p>GD Library: <?php echo $gd_status; ?></p>
            <p>WebP Support: <?php echo $webp_status; ?></p>

            <div style="margin-bottom: 15px;">
                <button id="aimo-optimize-selected" class="button button-primary" style="margin-right: 10px;">
                    ✓ Optimize Selected (Convert to WebP)
                </button>
                <button id="aimo-select-all" class="button">Select All</button>
                <button id="aimo-deselect-all" class="button">Deselect All</button>
                <button id="aimo-load-more" class="button" style="display: none;">Load More</button>
            </div>

            <div id="aimo-loading" style="display: none; color: #0073aa;">
                <p>⏳ Loading images...</p>
            </div>

            <div id="aimo-status" style="display: none; padding: 10px; margin-bottom: 15px; border-radius: 4px;"></div>

            <table id="aimo-images-table" style="width: 100%; border-collapse: collapse; display: none;">
                <thead>
                    <tr style="background: #f5f5f5; border-bottom: 1px solid #ddd;">
                        <th style="padding: 10px; text-align: left;"><input type="checkbox" id="aimo-select-checkbox"></th>
                        <th style="padding: 10px; text-align: left;">Thumbnail</th>
                        <th style="padding: 10px; text-align: left;">File Name</th>
                        <th style="padding: 10px; text-align: center;">Type</th>
                        <th style="padding: 10px; text-align: right;">Size</th>
                        <th style="padding: 10px; text-align: center;">Status</th>
                        <th style="padding: 10px; text-align: center;">WebP?</th>
                        <th style="padding: 10px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="aimo-images-list"></tbody>
            </table>

            <div id="aimo-no-images" style="padding: 20px; text-align: center; color: #666;">
                No images found in media library
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        const ajaxurl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
        const nonce = '<?php echo wp_create_nonce('aimo_nonce'); ?>';
        let currentPage = 1;
        let maxPages = 1;

        // Load images on page load
        loadImages();

        function loadImages(paged = 1, append = false) {
            $('#aimo-loading').show();
            if (!append) {
                $('#aimo-images-table').hide();
                $('#aimo-no-images').hide();
                $('#aimo-images-list').empty();
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: { 
                    action: 'aimo_get_images',
                    nonce: nonce,
                    paged: paged
                },
                dataType: 'json',
                success: function(response) {
                    $('#aimo-loading').hide();
                    console.log('AIMO Load Images Response:', response);

                    if (!response.success || response.data.images.length === 0) {
                        $('#aimo-no-images').show();
                        return;
                    }

                    maxPages = response.data.max_pages;
                    currentPage = paged;

                    let html = append ? $('#aimo-images-list').html() : '';
                    response.data.images.forEach(function(img) {
                        console.log(img);
                        const status = img.optimized ? '<span style="color: green;">✓ Optimized</span>' : '<span style="color: orange;">Not optimized</span>';
                        const webp = img.has_webp ? '<span style="color: green;">✓ Yes</span>' : '<span style="color: red;">No</span>';
                        const thumb = img.thumb ? `<img src="${img.thumb}" style="max-width: 50px; max-height: 50px;">` : 'No thumbnail';
                        const typeDisplay = img.mime === 'image/webp' ? 'WebP' : img.original_mime.split('/')[1].toUpperCase();
                        html += `
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;"><input type="checkbox" class="aimo-image-checkbox" value="${img.id}"></td>
                                <td style="padding: 10px;">${thumb}</td>
                                <td style="padding: 10px;"><a href="${img.url}" target="_blank">${img.name}</a></td>
                                <td style="padding: 10px; text-align: center;">${typeDisplay}</td>
                                <td style="padding: 10px; text-align: right; font-weight: bold;">${img.size_display}</td>
                                <td style="padding: 10px; text-align: center;">${status}</td>
                                <td style="padding: 10px; text-align: center;">${webp}</td>
                                <td style="padding: 10px; text-align: center;">
                                    <button class="aimo-optimize-single button button-small" data-id="${img.id}" ${img.optimized ? 'disabled' : ''}>Optimize + WebP</button>
                                    <button class="aimo-debug-webp button button-small" data-id="${img.id}" style="margin-left: 5px;">Debug WebP</button>
                                </td>
                            </tr>
                        `;
                    });

                    $('#aimo-images-list').html(html);
                    $('#aimo-images-table').show();
                    $('#aimo-load-more').toggle(currentPage < maxPages);

                    // Bind buttons
                    $('.aimo-optimize-single').off('click').on('click', function() {
                        optimizeSingleImage($(this).data('id'), $(this));
                    });
                    $('.aimo-debug-webp').off('click').on('click', function() {
                        debugWebp($(this).data('id'), $(this));
                    });
                },
                error: function(xhr, status, error) {
                    $('#aimo-loading').hide();
                    console.log('AIMO Load Images Error:', status, error, xhr.responseText);
                    showStatus('✗ Failed to load images: ' + (xhr.responseText || error), 'error');
                }
            });
        }

        function optimizeSingleImage(imageId, btn) {
            btn.prop('disabled', true).text('⏳');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: { 
                    action: 'aimo_optimize_image', 
                    nonce: nonce,
                    image_id: imageId 
                },
                dataType: 'json',
                success: function(response) {
                    console.log('AIMO Optimize Single Response:', response);
                    if (response.success) {
                        btn.text('✓').css('color', 'green').prop('disabled', true);
                        showStatus('✓ Image optimized and converted to WebP! Saved: ' + response.data.saved + ' (' + response.data.savings_percent + '%)', 'success');
                        setTimeout(() => loadImages(currentPage), 1500);
                    } else {
                        showStatus('✗ Failed to optimize: ' + (response.data.message || 'Unknown error'), 'error');
                        btn.text('Optimize + WebP').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AIMO Optimize Single Error:', status, error, xhr.responseText);
                    showStatus('✗ Error occurred: ' + (xhr.responseText || error), 'error');
                    btn.text('Optimize + WebP').prop('disabled', false);
                }
            });
        }

        function debugWebp(imageId, btn) {
            btn.prop('disabled', true).text('⏳');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: { 
                    action: 'aimo_debug_webp', 
                    nonce: nonce,
                    image_id: imageId 
                },
                dataType: 'json',
                success: function(response) {
                    console.log('AIMO Debug WebP Response:', response);
                    if (response.success) {
                        let message = `Debug for ID ${imageId}:\n` +
                                      `File: ${response.data.file}\n` +
                                      `File Exists: ${response.data.file_exists}\n` +
                                      `File Writable: ${response.data.file_writable}\n` +
                                      `File Size: ${response.data.file_size ? (response.data.file_size / 1024).toFixed(2) + ' KB' : 'N/A'}\n` +
                                      `MIME Type: ${response.data.mime_type}\n` +
                                      `Uploads Dir: ${response.data.uploads_dir}\n` +
                                      `Uploads Writable: ${response.data.uploads_writable}\n` +
                                      `GD Loaded: ${response.data.gd_loaded}\n` +
                                      `WebP Support: ${response.data.webp_support}\n` +
                                      (response.data.webp_replaced ? `WebP Replaced: ${response.data.webp_replaced}\n` +
                                      `New File Size: ${response.data.new_file_size ? (response.data.new_file_size / 1024).toFixed(2) + ' KB' : 'N/A'}\n` +
                                      `New MIME Type: ${response.data.new_mime_type}` : '');
                        showStatus(message, 'info');
                        btn.text('Debug WebP').prop('disabled', false);
                        setTimeout(() => loadImages(currentPage), 1500);
                    } else {
                        showStatus('✗ Debug failed: ' + (response.data.message || 'Unknown error'), 'error');
                        btn.text('Debug WebP').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AIMO Debug WebP Error:', status, error, xhr.responseText);
                    showStatus('✗ Debug error: ' + (xhr.responseText || error), 'error');
                    btn.text('Debug WebP').prop('disabled', false);
                }
            });
        }

        $('#aimo-select-all').on('click', function() {
            $('.aimo-image-checkbox').prop('checked', true);
        });

        $('#aimo-deselect-all').on('click', function() {
            $('.aimo-image-checkbox').prop('checked', false);
        });

        $('#aimo-select-checkbox').on('change', function() {
            $('.aimo-image-checkbox').prop('checked', $(this).prop('checked'));
        });

        $('#aimo-optimize-selected').on('click', function() {
            const selected = $('.aimo-image-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selected.length === 0) {
                showStatus('⚠ Please select images to optimize', 'warning');
                return;
            }

            $(this).prop('disabled', true).text('⏳ Processing...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: { 
                    action: 'aimo_optimize_all',
                    nonce: nonce,
                    image_ids: selected
                },
                dataType: 'json',
                success: function(response) {
                    console.log('AIMO Optimize All Response:', response);
                    if (response.success) {
                        showStatus('✓ Batch optimized and converted to WebP! Optimized: ' + response.data.optimized + ' | Failed: ' + response.data.failed + ' | Saved: ' + response.data.total_saved, 'success');
                        $('#aimo-optimize-selected').prop('disabled', false).text('✓ Optimize Selected (Convert to WebP)');
                        setTimeout(() => loadImages(currentPage), 1500);
                    } else {
                        showStatus('✗ Failed to optimize: ' + (response.data.message || 'Unknown error'), 'error');
                        $('#aimo-optimize-selected').prop('disabled', false).text('✓ Optimize Selected (Convert to WebP)');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AIMO Optimize All Error:', status, error, xhr.responseText);
                    showStatus('✗ Error occurred: ' + (xhr.responseText || error), 'error');
                    $('#aimo-optimize-selected').prop('disabled', false).text('✓ Optimize Selected (Convert to WebP)');
                }
            });
        });

        $('#aimo-load-more').on('click', function() {
            if (currentPage < maxPages) {
                loadImages(currentPage + 1, true);
            }
        });

        function showStatus(message, type) {
            const colors = {
                success: '#d4edda',
                error: '#f8d7da',
                warning: '#fff3cd',
                info: '#d1ecf1'
            };
            const textColors = {
                success: '#155724',
                error: '#721c24',
                warning: '#856404',
                info: '#0c5460'
            };

            $('#aimo-status')
                .html(message.replace(/\n/g, '<br>'))
                .css('background-color', colors[type] || colors.success)
                .css('color', textColors[type] || textColors.success)
                .show()
                .delay(10000)
                .fadeOut();
        }
    });
    </script>
    <?php
}

function aimo_disable_image_sizes() {
    // Remove default WordPress image sizes
    remove_image_size('thumbnail');  // 
    remove_image_size('medium');
    remove_image_size('large');

    // Disable additional WordPress image sizes
    add_filter('intermediate_image_sizes_advanced', function($sizes) {
        // Unset all intermediate sizes you don't want
        unset($sizes['medium']);
        unset($sizes['large']);
        unset($sizes['medium_large']);
        unset($sizes['1536x1536']);
        unset($sizes['2048x2048']);
        unset($sizes['thumbnail']); // Ensure 150x150 is removed
        return $sizes;
    });
}

add_action('init', 'aimo_disable_image_sizes');


?>
