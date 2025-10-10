<?php
/*
Plugin Name: Auto Image & Media Optimizer
Description: Automatically optimizes images and media files in WordPress, improving site speed and performance.
Version: 6.0
Author: Your Name
License: GPL2
*/

define('AIMO_VERSION', '6.0');

register_activation_hook(__FILE__, 'aimo_activate');
register_deactivation_hook(__FILE__, 'aimo_deactivate');

function aimo_activate() {
    if (!wp_next_scheduled('aimo_optimize_cron_hook')) {
        wp_schedule_event(time(), 'hourly', 'aimo_optimize_cron_hook');
    }
}

function aimo_deactivate() {
    wp_clear_scheduled_hook('aimo_optimize_cron_hook');
}

add_action('aimo_optimize_cron_hook', function() {
    aimo_batch_process();
});

// Auto-optimize on upload
add_filter('wp_generate_attachment_metadata', function($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    if ($file && file_exists($file)) {
        aimo_compress_image($file);
        error_log("AIMO: Auto-optimized uploaded image ID $attachment_id");
    }
    return $metadata;
}, 10, 2);

// AJAX endpoints
add_action('wp_ajax_aimo_get_images', 'aimo_get_images_ajax');
add_action('wp_ajax_aimo_optimize_image', 'aimo_optimize_image_ajax');
add_action('wp_ajax_aimo_optimize_all', 'aimo_optimize_all_ajax');

function aimo_get_images_ajax() {
    if (!current_user_can('manage_options')) {
        wp_die(json_encode(['success' => false, 'message' => 'Unauthorized']));
    }
    
    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_status' => 'inherit'
    ));
    
    $images = array();
    foreach ($attachments as $attachment) {
        $file = get_attached_file($attachment->ID);
        $mime_type = get_post_mime_type($attachment->ID);
        
        if ($file && file_exists($file) && strpos($mime_type, 'image') !== false) {
            clearstatcache(true, $file);
            $size = filesize($file);
            $is_optimized = get_post_meta($attachment->ID, '_aimo_optimized', true);
            
            $images[] = array(
                'id' => $attachment->ID,
                'name' => basename($file),
                'size' => $size,
                'size_display' => size_format($size, 2),
                'mime' => $mime_type,
                'optimized' => $is_optimized ? true : false,
                'url' => wp_get_attachment_url($attachment->ID),
                'thumb' => wp_get_attachment_thumb_url($attachment->ID)
            );
        }
    }
    
    wp_die(json_encode([
        'success' => true,
        'images' => $images,
        'total' => count($images)
    ]));
}

function aimo_optimize_image_ajax() {
    if (!current_user_can('manage_options')) {
        wp_die(json_encode(['success' => false, 'message' => 'Unauthorized']));
    }
    
    if (!extension_loaded('gd')) {
        error_log("AIMO AJAX: GD Library not installed");
        wp_die(json_encode(['success' => false, 'message' => 'GD Library not installed on server']));
    }
    
    $image_id = intval($_POST['image_id']);
    $file = get_attached_file($image_id);
    
    if (!$file || !file_exists($file)) {
        error_log("AIMO AJAX: File not found for ID $image_id");
        wp_die(json_encode(['success' => false, 'message' => 'File not found']));
    }
    
    error_log("AIMO AJAX: Starting optimization of file: $file");
    
    clearstatcache(true, $file);
    $before_size = filesize($file);
    
    $result = aimo_compress_image($file);
    
    error_log("AIMO AJAX: Compression result: " . ($result ? 'true' : 'false'));
    
    if ($result) {
        sleep(1); // Wait a moment for file write to complete
        clearstatcache(true, $file);
        $after_size = filesize($file);
        $saved = $before_size - $after_size;
        
        error_log("AIMO AJAX: Before: $before_size | After: $after_size | Saved: $saved");
        
        update_post_meta($image_id, '_aimo_optimized', time());
        
        wp_die(json_encode([
            'success' => true,
            'before' => size_format($before_size, 2),
            'after' => size_format($after_size, 2),
            'saved' => size_format($saved, 2),
            'savings_percent' => $before_size > 0 ? round(($saved / $before_size) * 100, 2) : 0
        ]));
    } else {
        error_log("AIMO AJAX: Optimization failed");
        wp_die(json_encode([
            'success' => false,
            'message' => 'Could not optimize image. Check debug log for details.'
        ]));
    }
}

function aimo_optimize_all_ajax() {
    if (!current_user_can('manage_options')) {
        wp_die(json_encode(['success' => false, 'message' => 'Unauthorized']));
    }
    
    if (!extension_loaded('gd')) {
        wp_die(json_encode(['success' => false, 'message' => 'GD Library not installed']));
    }
    
    $image_ids = isset($_POST['image_ids']) ? array_map('intval', $_POST['image_ids']) : array();
    
    $optimized = 0;
    $total_saved = 0;
    $failed = 0;
    
    foreach ($image_ids as $image_id) {
        $file = get_attached_file($image_id);
        
        if ($file && file_exists($file)) {
            clearstatcache(true, $file);
            $before_size = filesize($file);
            
            $result = aimo_compress_image($file);
            
            if ($result) {
                clearstatcache(true, $file);
                $after_size = filesize($file);
                $saved = $before_size - $after_size;
                
                if ($saved > 0) {
                    $total_saved += $saved;
                    $optimized++;
                    update_post_meta($image_id, '_aimo_optimized', time());
                }
            } else {
                $failed++;
            }
        }
    }
    
    error_log("AIMO: Batch optimization completed. Optimized: $optimized | Failed: $failed | Total saved: $total_saved bytes");
    
    wp_die(json_encode([
        'success' => true,
        'optimized' => $optimized,
        'failed' => $failed,
        'total_saved' => size_format($total_saved, 2)
    ]));
}

function aimo_compress_image($file_path) {
    if (!file_exists($file_path)) {
        error_log("AIMO: File not found: $file_path");
        return false;
    }
    
    if (!extension_loaded('gd')) {
        error_log("AIMO: GD Library not loaded");
        return false;
    }
    
    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $jpeg_quality = (int) get_option('aimo_jpeg_quality', 65);
    $png_quality = (int) get_option('aimo_png_quality', 6);
    
    $jpeg_quality = max(1, min(100, $jpeg_quality));
    $png_quality = max(0, min(9, $png_quality));
    
    try {
        // Create temp file path
        $temp_path = $file_path . '.aimo.tmp';
        
        if ($ext === 'jpg' || $ext === 'jpeg') {
            if (function_exists('imagecreatefromjpeg')) {
                $image = @imagecreatefromjpeg($file_path);
                if ($image !== false) {
                    imageinterlace($image, true);
                    $result = @imagejpeg($image, $temp_path, $jpeg_quality);
                    imagedestroy($image);
                    
                    if ($result && file_exists($temp_path) && filesize($temp_path) > 100) {
                        if (@copy($temp_path, $file_path)) {
                            @unlink($temp_path);
                            error_log("AIMO: JPEG optimized successfully");
                            return true;
                        }
                        @unlink($temp_path);
                    }
                } else {
                    error_log("AIMO: Failed to load JPEG from $file_path");
                }
            } else {
                error_log("AIMO: imagecreatefromjpeg not available");
            }
        } 
        elseif ($ext === 'png') {
            if (function_exists('imagecreatefrompng')) {
                $image = @imagecreatefrompng($file_path);
                if ($image !== false) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    $result = @imagepng($image, $temp_path, $png_quality, PNG_ALL_FILTERS);
                    imagedestroy($image);
                    
                    if ($result && file_exists($temp_path) && filesize($temp_path) > 100) {
                        if (@copy($temp_path, $file_path)) {
                            @unlink($temp_path);
                            error_log("AIMO: PNG optimized successfully");
                            return true;
                        }
                        @unlink($temp_path);
                    }
                } else {
                    error_log("AIMO: Failed to load PNG from $file_path");
                }
            } else {
                error_log("AIMO: imagecreatefrompng not available");
            }
        }
        elseif ($ext === 'webp') {
            if (function_exists('imagecreatefromwebp')) {
                $image = @imagecreatefromwebp($file_path);
                if ($image !== false) {
                    $result = @imagewebp($image, $temp_path, $jpeg_quality);
                    imagedestroy($image);
                    
                    if ($result && file_exists($temp_path) && filesize($temp_path) > 100) {
                        if (@copy($temp_path, $file_path)) {
                            @unlink($temp_path);
                            error_log("AIMO: WebP optimized successfully");
                            return true;
                        }
                        @unlink($temp_path);
                    }
                } else {
                    error_log("AIMO: Failed to load WebP from $file_path");
                }
            } else {
                error_log("AIMO: imagecreatefromwebp not available");
            }
        }
        elseif ($ext === 'gif') {
            if (function_exists('imagecreatefromgif')) {
                $image = @imagecreatefromgif($file_path);
                if ($image !== false) {
                    $result = @imagegif($image, $temp_path);
                    imagedestroy($image);
                    
                    if ($result && file_exists($temp_path) && filesize($temp_path) > 100) {
                        if (@copy($temp_path, $file_path)) {
                            @unlink($temp_path);
                            error_log("AIMO: GIF optimized successfully");
                            return true;
                        }
                        @unlink($temp_path);
                    }
                } else {
                    error_log("AIMO: Failed to load GIF from $file_path");
                }
            } else {
                error_log("AIMO: imagecreatefromgif not available");
            }
        } else {
            error_log("AIMO: Unsupported format: $ext");
        }
        
    } catch (Exception $e) {
        error_log("AIMO Exception: " . $e->getMessage());
    }
    
    return false;
}

function aimo_batch_process() {
    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => 5,
        'post_status' => 'inherit',
        'meta_query' => array(
            array(
                'key' => '_aimo_optimized',
                'compare' => 'NOT EXISTS'
            )
        )
    ));
    
    foreach ($attachments as $attachment) {
        $file = get_attached_file($attachment->ID);
        if ($file && file_exists($file)) {
            if (aimo_compress_image($file)) {
                update_post_meta($attachment->ID, '_aimo_optimized', time());
            }
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
    register_setting('aimo_settings_group', 'aimo_jpeg_quality');
    register_setting('aimo_settings_group', 'aimo_png_quality');
    
    add_settings_section('aimo_section', 'Compression Settings', function() {
        echo '<p>Configure compression levels for images</p>';
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
});

function aimo_render_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $gd_status = extension_loaded('gd') ? '<span style="color: green;">✓ Installed</span>' : '<span style="color: red;">✗ Not Installed</span>';
    ?>
    <div style="max-width: 1200px; margin: 20px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <h1>Auto Image & Media Optimizer</h1>
        
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
            
            <div style="margin-bottom: 15px;">
                <button id="aimo-optimize-selected" class="button button-primary" style="margin-right: 10px;">
                    ✓ Optimize Selected
                </button>
                <button id="aimo-select-all" class="button">Select All</button>
                <button id="aimo-deselect-all" class="button">Deselect All</button>
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
        const ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        
        // Load images on page load
        loadImages();
        
        function loadImages() {
            $('#aimo-loading').show();
            $('#aimo-images-table').hide();
            $('#aimo-no-images').hide();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: { action: 'aimo_get_images' },
                dataType: 'json',
                success: function(response) {
                    $('#aimo-loading').hide();
                    
                    if (response.images.length === 0) {
                        $('#aimo-no-images').show();
                        return;
                    }
                    
                    let html = '';
                    response.images.forEach(function(img) {
                        const status = img.optimized ? '<span style="color: green;">✓ Optimized</span>' : '<span style="color: orange;">Not optimized</span>';
                        html += `
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;"><input type="checkbox" class="aimo-image-checkbox" value="${img.id}"></td>
                                <td style="padding: 10px;"><img src="${img.thumb}" style="max-width: 50px; max-height: 50px;"></td>
                                <td style="padding: 10px;"><a href="${img.url}" target="_blank">${img.name}</a></td>
                                <td style="padding: 10px; text-align: center;">${img.mime.split('/')[1]}</td>
                                <td style="padding: 10px; text-align: right; font-weight: bold;">${img.size_display}</td>
                                <td style="padding: 10px; text-align: center;">${status}</td>
                                <td style="padding: 10px; text-align: center;">
                                    <button class="aimo-optimize-single button button-small" data-id="${img.id}">Optimize</button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    $('#aimo-images-list').html(html);
                    $('#aimo-images-table').show();
                    
                    // Bind single optimize buttons
                    $('.aimo-optimize-single').on('click', function() {
                        optimizeSingleImage($(this).data('id'), $(this));
                    });
                }
            });
        }
        
        function optimizeSingleImage(imageId, btn) {
            btn.prop('disabled', true).text('⏳');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: { action: 'aimo_optimize_image', image_id: imageId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        btn.text('✓').css('color', 'green').prop('disabled', false);
                        showStatus('✓ Image optimized! Saved: ' + response.saved + ' (' + response.savings_percent + '%)', 'success');
                        setTimeout(loadImages, 1500);
                    } else {
                        showStatus('✗ Failed to optimize', 'error');
                        btn.text('Optimize').prop('disabled', false);
                    }
                },
                error: function() {
                    showStatus('✗ Error occurred', 'error');
                    btn.text('Optimize').prop('disabled', false);
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
                    image_ids: selected
                },
                dataType: 'json',
                success: function(response) {
                    showStatus('✓ Optimized: ' + response.optimized + ' | Failed: ' + response.failed + ' | Saved: ' + response.total_saved, 'success');
                    $('#aimo-optimize-selected').prop('disabled', false).text('✓ Optimize Selected');
                    setTimeout(loadImages, 1500);
                },
                error: function() {
                    showStatus('✗ Error occurred', 'error');
                    $('#aimo-optimize-selected').prop('disabled', false).text('✓ Optimize Selected');
                }
            });
        });
        
        function showStatus(message, type) {
            const colors = {
                success: '#d4edda',
                error: '#f8d7da',
                warning: '#fff3cd'
            };
            const textColors = {
                success: '#155724',
                error: '#721c24',
                warning: '#856404'
            };
            
            $('#aimo-status')
                .html(message)
                .css('background-color', colors[type] || colors.success)
                .css('color', textColors[type] || textColors.success)
                .show()
                .delay(5000)
                .fadeOut();
        }
    });
    </script>
    <?php
}
?>