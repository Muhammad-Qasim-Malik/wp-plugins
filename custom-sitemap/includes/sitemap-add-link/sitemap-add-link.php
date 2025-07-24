<?php
function add_link_to_sitemap_page() {
    global $wpdb;

    $post_types = get_post_types(['public' => true], 'objects');
    $sitemaps = $wpdb->get_results("SELECT DISTINCT sitemap_name FROM {$wpdb->prefix}sitemap_links");
    ?>

    <div class="wrap">
        <h1>Add Link to Sitemap</h1>

        <form id="add-link-form" style="max-width:600px; background:#fff; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1); border-radius:4px;">
            <?php wp_nonce_field('add_link_action', 'add_link_nonce'); ?>

            <label for="post_type" style="font-weight:600; display:block; margin-bottom:6px;">Select Post Type</label>
            <select name="post_type" id="post_type" required style="width:100%; padding:8px; margin-bottom:20px; font-size:14px; border:1px solid #ccc; border-radius:4px;">
                <option value="">-- Select Post Type --</option>
                <?php foreach ($post_types as $ptype): ?>
                    <option value="<?php echo esc_attr($ptype->name); ?>"><?php echo esc_html($ptype->label); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="post_id" style="font-weight:600; display:block; margin-bottom:6px;">Select Post / Link</label>
            <select name="post_id" id="post_id" required style="width:100%; padding:8px; margin-bottom:20px; font-size:14px; border:1px solid #ccc; border-radius:4px;">
                <option value="">-- Select Post Type First --</option>
            </select>

            <label for="sitemap_name" style="font-weight:600; display:block; margin-bottom:6px;">Select Sitemap</label>
            <select name="sitemap_name" id="sitemap_name" required style="width:100%; padding:8px; margin-bottom:20px; font-size:14px; border:1px solid #ccc; border-radius:4px;">
                <option value="">-- Select Sitemap --</option>
                <?php foreach ($sitemaps as $sm): ?>
                    <option value="<?php echo esc_attr($sm->sitemap_name); ?>"><?php echo esc_html($sm->sitemap_name); ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="button button-primary">Add Link</button>
        </form>

        <div id="add-link-message" style="margin-top:15px;"></div>
    </div>

    <script>
    (function($){
        $('#post_type').on('change', function() {
            var postType = $(this).val();
            $('#post_id').html('<option>Loading posts...</option>');
            if(!postType) {
                $('#post_id').html('<option value="">-- Select Post Type First --</option>');
                return;
            }

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'get_posts_by_post_type',
                    post_type: postType,
                    security: '<?php echo wp_create_nonce("get_posts_nonce"); ?>'
                },
                success: function(response) {
                    if(response.success) {
                        var options = '<option value="">-- Select Post --</option>';
                        $.each(response.data, function(i, post) {
                            options += '<option value="' + post.id + '">' + post.title + '</option>';
                        });
                        $('#post_id').html(options);
                    } else {
                        $('#post_id').html('<option value="">No posts found</option>');
                    }
                },
                error: function() {
                    $('#post_id').html('<option value="">Error loading posts</option>');
                }
            });
        });

       $('#add-link-form').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();  // includes post_type, post_id, sitemap_name, add_link_nonce

            $.ajax({
                url: sitemap_gen.ajax_url,
                method: 'POST',
                data: formData + '&action=add_link',
                success: function(response) {
                    console.log(response);
                    // handle response
                    $('#add-link-message').html(response.data.message);
                },
                error: function(xhr, status, error) {
                    $('#add-link-message').html(response.data.message);

                }
            });
        });


    })(jQuery);
    </script>

    <?php
}
?>
