<?php
function backstage_admin_page_html() {
    if (isset($_POST['submit'])) {
        if (!empty($_FILES['background_image']['name'])) {
            $uploaded_file = $_FILES['background_image'];
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($uploaded_file, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                update_option('backstage_bg_image', $movefile['url']);
                echo '<div class="notice notice-success is-dismissible"><p>Background image uploaded successfully!</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>Error uploading file: ' . esc_html($movefile['error']) . '</p></div>';
            }
        }
    }

    $background_image = get_option('backstage_bg_image');
    ?>
    <div class="wrap backstage-wrap">
        <h1><?php esc_html_e('Backstage Settings', 'backstage'); ?></h1>
        <p><?php esc_html_e('Upload a background image that will be used in your shortcodes.', 'backstage'); ?></p>
        
        <form method="post" enctype="multipart/form-data" class="backstage-form">
            <div class="backstage-form-field">
                <label for="background_image"><?php esc_html_e('Choose Background Image:', 'backstage'); ?></label>
                <input type="file" name="background_image" id="background_image" accept="image/*" required>
            </div>
            <input type="submit" name="submit" value="<?php esc_attr_e('Upload', 'backstage'); ?>" class="button button-primary backstage-submit">
        </form>

        <?php if ($background_image) : ?>
            <h2><?php esc_html_e('Current Background Image:', 'backstage'); ?></h2>
            <div class="backstage-image-preview">
                <img src="<?php echo esc_url($background_image); ?>" alt="<?php esc_attr_e('Background Image', 'backstage'); ?>">
            </div>
        <?php endif; ?>
    </div>
    <?php
}
?>