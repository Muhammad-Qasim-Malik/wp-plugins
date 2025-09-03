<?php
function mq_maintenance_save_settings() {
    if ( isset($_POST['mq_maintenance_nonce']) 
        && wp_verify_nonce($_POST['mq_maintenance_nonce'], 'mq_maintenance_save') ) {
        
        update_option('maintenance_mode', isset($_POST['maintenance_mode']) ? 1 : 0);

        if ( isset($_POST['maintenance_heading']) ) {
            update_option('maintenance_heading', sanitize_text_field($_POST['maintenance_heading']));
        }

        if ( isset($_POST['maintenance_paragraph']) ) {
            update_option('maintenance_paragraph', sanitize_textarea_field($_POST['maintenance_paragraph']));
        }

        if ( isset($_FILES['maintenance_background']) && ! empty($_FILES['maintenance_background']['name']) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );

            $uploadedfile     = $_FILES['maintenance_background'];
            $upload_overrides = array( 'test_form' => false );

            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

            if ( $movefile && ! isset( $movefile['error'] ) ) {
                update_option( 'maintenance_background', esc_url_raw( $movefile['url'] ) );
            } else {
                error_log( 'Maintenance upload failed: ' . $movefile['error'] );
            }
        }

        update_option('maintenance_subscribe', isset($_POST['maintenance_subscribe']) ? 1 : 0);
    }
}
add_action('admin_init', 'mq_maintenance_save_settings');


function mq_page_loader_html() {
    ?>
    <div id="mq-loader">
        <div class="mq-spinner"></div>
    </div>
    <?php
}
add_action('wp_footer', 'mq_page_loader_html');