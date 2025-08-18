<?php 
add_action('admin_menu', function() {
    add_menu_page(
        'MQ Weather Report',     
        'MQ Weather Report',      
        'manage_options',         
        'mqwr',                  
        'mqwr_admin_page',        
        'dashicons-cloud',       
        25                       
    );
});

function mqwr_admin_page() { ?>
    <div class="wrap">
        <h1>MQ Weather Report Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('mqwr_settings'); ?>
            <?php do_settings_sections('mqwr_settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">OpenWeatherMap API Key</th>
                    <td><input type="text" name="mqwr_api_key" value="<?php echo esc_attr(get_option('mqwr_api_key')); ?>" class="regular-text"></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }

add_action('admin_init', function() {
    register_setting('mqwr_settings', 'mqwr_api_key', 'sanitize_text_field');
});

