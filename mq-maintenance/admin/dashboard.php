<?php
if ( ! function_exists('mq_maintenance_dashboard') ) {
    function mq_maintenance_dashboard() {
        ?>
        <div class="wrap">
            <h1>MQ Maintenance</h1>
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('mq_maintenance_save', 'mq_maintenance_nonce'); ?>

                <p>
                    <label for="maintenance-mode">
                        <input type="checkbox" name="maintenance_mode" id="maintenance-mode" 
                               value="1" <?php checked(1, get_option('maintenance_mode')); ?>>
                        Enable Maintenance Mode
                    </label>
                </p>

                <p>
                    <label for="maintenance-heading">Maintenance Heading</label><br>
                    <input type="text" name="maintenance_heading" id="maintenance-heading"
                           value="<?php echo esc_attr(get_option('maintenance_heading')); ?>" class="regular-text">
                </p>

                <p>
                    <label for="maintenance-paragraph">Maintenance Paragraph</label><br>
                    <textarea name="maintenance_paragraph" id="maintenance-paragraph" rows="5" class="large-text"><?php 
                        echo esc_textarea(get_option('maintenance_paragraph')); 
                    ?></textarea>
                </p>
                <?php $bg = get_option('maintenance_background'); ?>
                <p>
                    <label for="maintenance-background">Maintenance Background Image</label><br>
                    <input type="file" name="maintenance_background" id="maintenance-background">
                    <?php if ( $bg ) : ?>
                        <img src="<?php echo esc_url($bg); ?>" style="max-width:200px; height:auto; margin-top:10px;">
                    <?php endif; ?>
                </p>

                <p>
                    <label for="maintenance-subscribe">
                        <input type="checkbox" name="maintenance_subscribe" id="maintenance-subscribe"
                               value="1" <?php checked(1, get_option('maintenance_subscribe')); ?>>
                        Show Subscribe Form
                    </label>
                </p>

                <?php submit_button('Save Settings'); ?>
            </form>
        </div>
        <?php
    }
}
