<?php
function mq_single_post_template() {
    // if (!is_user_logged_in()) {
    //     return '<p>You must be logged in to view this academy post.</p>';
    // }

    $current_user = wp_get_current_user();
    $post_id = get_the_ID();
    $post_author_id = get_post_field('post_author', $post_id);
    $post_author = get_user_by('id', $post_author_id);
    $role = $post_author->roles[0];

    ob_start();
    ?>
    <div class="mq-single-academy">
        <h2><?php the_title(); ?></h2>
        <?php if (has_post_thumbnail()) { ?>
            <div class="academy-featured-image">
                <!-- <h3>Featured Image</h3> -->
                <?php the_post_thumbnail('full'); ?>
            </div>
        <?php } ?>
        
        <div class="academy-details">
            <?php if ($role == 'paid_member') { ?>
                <p><?php echo esc_textarea(get_post_meta($post_id, 'muay_thai_short_bio', true)); ?></p>
            <?php }?>
            <p><strong>Location:</strong> <?php echo esc_html(get_post_meta($post_id, 'muay_thai_location', true)); ?></p>
            <p><strong>Phone:</strong> <?php echo esc_html(get_post_meta($post_id, 'muay_thai_phone', true)); ?></p>
            <p><strong>Website:</strong> 
                <a href="<?php echo esc_url(get_post_meta($post_id, 'muay_thai_website', true)); ?>" class="mq-button" target="_blank">Visit Website</a>
            </p>

            <?php if ($role == 'paid_member') { ?>
                <p><strong>Calendar:</strong> 
                    <a href="<?php echo esc_url(get_post_meta($post_id, 'muay_thai_calendar_link', true)); ?>" class="mq-button" target="_blank">Book Now</a>
                </p>
                <div class="academy-images">
                    <?php
                    $image_ids = get_post_meta($post_id, 'muay_thai_multiple_images', true);
                    if ($image_ids) {
                        foreach ($image_ids as $image_id) {
                            $image_url = wp_get_attachment_url($image_id);
                            echo '<img src="' . esc_url($image_url) . '" alt="Academy Image" class="academy-image" />';
                        }
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('mq_single_post', 'mq_single_post_template');
