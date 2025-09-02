<?php
function mq_settings_page() {
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'smtp';

    ?>
    <div class="wrap">
        <h1>MQ Leads Settings</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=mq-settings&tab=smtp" class="nav-tab <?php echo $active_tab == 'smtp' ? 'nav-tab-active' : ''; ?>">SMTP Settings</a>
            <a href="?page=mq-settings&tab=test" class="nav-tab <?php echo $active_tab == 'test' ? 'nav-tab-active' : ''; ?>">Test Email</a>
            <a href="?page=mq-settings&tab=cron" class="nav-tab <?php echo $active_tab == 'cron' ? 'nav-tab-active' : ''; ?>">Cron Job</a>
        </h2>

        <form method="post">
            <?php wp_nonce_field('mq_save_smtp_settings','mq_smtp_nonce'); ?>

            <?php if ($active_tab == 'smtp'): 
                $smtp_host = get_option('mq_smtp_host', '');
                $smtp_port = get_option('mq_smtp_port', 587);
                $smtp_user = get_option('mq_smtp_user', '');
                $smtp_pass = get_option('mq_smtp_pass', '');
                $smtp_secure = get_option('mq_smtp_secure', 'tls');
            ?>
                <table class="form-table">
                    <tr>
                        <th>SMTP Host</th>
                        <td><input type="text" name="mq_smtp_host" value="<?php echo esc_attr($smtp_host); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>SMTP Port</th>
                        <td><input type="number" name="mq_smtp_port" value="<?php echo esc_attr($smtp_port); ?>" class="small-text"></td>
                    </tr>
                    <tr>
                        <th>SMTP Username</th>
                        <td><input type="text" name="mq_smtp_user" value="<?php echo esc_attr($smtp_user); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>SMTP Password</th>
                        <td><input type="password" name="mq_smtp_pass" value="<?php echo esc_attr($smtp_pass); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>Encryption</th>
                        <td>
                            <select name="mq_smtp_secure">
                                <option value="tls" <?php selected($smtp_secure, 'tls'); ?>>TLS</option>
                                <option value="ssl" <?php selected($smtp_secure, 'ssl'); ?>>SSL</option>
                                <option value="" <?php selected($smtp_secure, ''); ?>>None</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save SMTP Settings'); ?>

            <?php elseif ($active_tab == 'test'): 
                $test_email = get_option('mq_test_email', '');
            ?>
                <table class="form-table">
                    <tr>
                        <th>Send Test Email To</th>
                        <td><input type="email" name="mq_test_email" value="<?php echo esc_attr($test_email); ?>" class="regular-text"></td>
                    </tr>
                </table>
                <?php submit_button('Send Test Email'); ?>

            <?php elseif ($active_tab == 'cron'): 
                global $wpdb;
                $table_templates = $wpdb->prefix . 'mq_email_templates'; 
                $templates = $wpdb->get_results("SELECT id, template_name, subject FROM $table_templates", ARRAY_A);

                $cron_template = get_option('mq_cron_template', '');

                ?>
               <form method="post">
                    <?php wp_nonce_field('mq_save_settings', 'mq_settings_nonce'); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row">Cron Interval (minutes)</th>
                            <td>
                                <input type="number" name="mq_cron_interval" value="<?php echo esc_attr(get_option('mq_cron_interval', 5)); ?>" min="1" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Cron Email Template</th>
                            <td>
                                <select name="mq_cron_template">
                                    <option value="">-- Select Template --</option>
                                    <?php
                                    global $wpdb;
                                    $templates = $wpdb->get_results("SELECT id, template_name, subject FROM {$wpdb->prefix}mq_email_templates", ARRAY_A);
                                    $cron_template = get_option('mq_cron_template', '');
                                    if ($templates):
                                        foreach ($templates as $t): ?>
                                            <option value="<?php echo esc_attr($t['id']); ?>" <?php selected($cron_template, $t['id']); ?>>
                                                <?php echo esc_html($t['template_name']); ?> (<?php echo esc_html($t['subject']); ?>)
                                            </option>
                                        <?php endforeach;
                                    endif; ?>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <button type="submit" class="button button-primary">Save Cron Settings</button>
                    </p>
                </form>


                <?php // submit_button('Save Cron Settings'); ?>
            <?php endif; ?>
        </form>
    </div>
    <?php
}
