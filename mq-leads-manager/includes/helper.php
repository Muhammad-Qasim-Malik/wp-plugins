<?php

/******************************************************************************************/
/************************************Data Handler******************************************/
/******************************************************************************************/
function mq_insert_lead($lead_data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mq_leads';

    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE phone = %s",
        $lead_data['phone']
    ));

    if ($exists) return;

    $wpdb->insert($table_name, array(
        'business_name' => sanitize_text_field($lead_data['business_name']),
        'email'         => sanitize_email($lead_data['email']),
        'phone'         => sanitize_text_field($lead_data['phone']),
        'website'       => esc_url_raw($lead_data['website']),
        'niche'         => sanitize_text_field($lead_data['niche']),
        'address'       => sanitize_text_field($lead_data['address']),
        'city'          => sanitize_text_field($lead_data['city']),
        'country'       => sanitize_text_field($lead_data['country']),
        'status'        => isset($lead_data['status']) ? sanitize_text_field($lead_data['status']) : 'New',
        'date_added'    => current_time('mysql')
    ));
}

add_action('admin_init', 'mq_handle_import_csv');
function mq_handle_import_csv() {
    if (!isset($_POST['mq_import_submit']) || !isset($_FILES['mq_import_file'])) return;

    $file_type = $_FILES['mq_import_file']['type'];
    if ($file_type !== 'text/csv' && $file_type !== 'application/vnd.ms-excel') return;

    global $wpdb;
    $handle = fopen($_FILES['mq_import_file']['tmp_name'], 'r');
    if (!$handle) return;

    $row = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($row++ == 0) continue;

        mq_insert_lead(array(
            'business_name' => $data[0],
            'address'       => $data[1],
            'website'       => $data[2],
            'phone'         => $data[3],
            'niche'         => $data[4],
            'city'          => $data[5],
            'country'       => $data[6],
            'email'         => $data[7],
            'status'        => 'New'
        ));
    }

    fclose($handle);

    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p>Leads imported successfully!</p></div>';
    });
}

/******************************************************************************************/
/************************************Status Handler****************************************/
/******************************************************************************************/
add_action('wp_ajax_mq_update_status', 'mq_update_status');

function mq_update_status(){
    if(!current_user_can('manage_options')) wp_die();

    global $wpdb;
    $table = $wpdb->prefix . 'mq_leads';
    $ids = array_map('intval', $_POST['ids']);
    $status = sanitize_text_field($_POST['status']);

    if(!empty($ids) && $status){
        $ids_placeholders = implode(',', array_fill(0,count($ids),'%d'));
        $wpdb->query($wpdb->prepare(
            "UPDATE $table SET status=%s WHERE id IN ($ids_placeholders)",
            array_merge([$status], $ids)
        ));
    }
    wp_send_json_success();
}


/******************************************************************************************/
/************************************Delete Handler****************************************/
/******************************************************************************************/
add_action('wp_ajax_mq_delete_leads', 'mq_delete_leads');
function mq_delete_leads(){
    if(!current_user_can('manage_options')) wp_die();

    global $wpdb;
    $table = $wpdb->prefix . 'mq_leads';
    $ids = array_map('intval', $_POST['ids']);
    if(!empty($ids)){
        $ids_placeholders = implode(',', array_fill(0,count($ids),'%d'));
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $table WHERE id IN ($ids_placeholders)",
            $ids
        ));
    }
    wp_send_json_success();
}

add_action('wp_ajax_mq_bulk_delete_templates', 'mq_bulk_delete_templates');

function mq_bulk_delete_templates() {
     if(!current_user_can('manage_options')) wp_die();

    global $wpdb;
    $table = $wpdb->prefix . 'mq_email_templates';
    $ids = array_map('intval', $_POST['temp_ids']);
    if(!empty($ids)){
        $ids_placeholders = implode(',', array_fill(0,count($ids),'%d'));
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $table WHERE id IN ($ids_placeholders)",
            $ids
        ));
    }
    wp_send_json_success();
}

add_action('admin_init', 'mq_save_smtp_settings');

function mq_save_smtp_settings() {
    if (!isset($_POST['mq_smtp_nonce']) || !wp_verify_nonce($_POST['mq_smtp_nonce'], 'mq_save_smtp_settings')) {
        return;
    }

    if (isset($_POST['mq_smtp_host'])) update_option('mq_smtp_host', sanitize_text_field($_POST['mq_smtp_host']));
    if (isset($_POST['mq_smtp_port'])) update_option('mq_smtp_port', intval($_POST['mq_smtp_port']));
    if (isset($_POST['mq_smtp_user'])) update_option('mq_smtp_user', sanitize_text_field($_POST['mq_smtp_user']));
    if (isset($_POST['mq_smtp_pass'])) update_option('mq_smtp_pass', sanitize_text_field($_POST['mq_smtp_pass']));
    if (isset($_POST['mq_smtp_secure'])) update_option('mq_smtp_secure', sanitize_text_field($_POST['mq_smtp_secure']));

    if (isset($_POST['mq_test_email']) && is_email($_POST['mq_test_email'])) {
        $to = sanitize_email($_POST['mq_test_email']);
        update_option('mq_test_email', $to);

        add_action('phpmailer_init', 'mq_configure_phpmailer');

        $sent = wp_mail($to, 'Test Email from MQ Leads', 'This is a test email to verify SMTP settings.');

        if ($sent) {
            echo '<div class="notice notice-success is-dismissible"><p>Test email sent successfully!</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>There is an issue while sending your email!</p></div>';
        }
    }
}

function mq_configure_phpmailer($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = get_option('mq_smtp_host');
    $phpmailer->Port = get_option('mq_smtp_port');
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = get_option('mq_smtp_user');
    $phpmailer->Password = get_option('mq_smtp_pass');
    $secure = get_option('mq_smtp_secure');
    if ($secure) $phpmailer->SMTPSecure = $secure;
}

/******************************************************************************************/
/********************************Test Email for Templates**********************************/
/******************************************************************************************/
add_action('admin_post_mq_send_test_email', 'mq_send_test_email');
function mq_send_test_email() {
    if (!current_user_can('manage_options')) wp_die('Unauthorized');

    if (!isset($_POST['template_id'], $_POST['test_email'])) wp_die('Missing data');

    global $wpdb;
    $table = $wpdb->prefix . 'mq_email_templates';
    $id = intval($_POST['template_id']);
    $to = sanitize_email($_POST['test_email']);

    $template = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $id), ARRAY_A);
    if (!$template) wp_die('Template not found');

    $subject = $template['subject'];
    $body = $template['body'];

    $replacements = [
        '{business_name}' => 'Test Business',
        '{email}'        => 'test@example.com',
        '{phone}'        => '123456789',
        '{website}'      => 'https://example.com',
        '{niche}'        => 'Testing',
        '{address}'      => '123 Test St',
        '{city}'         => 'Test City',
        '{country}'      => 'Test Country',
        '{status}'       => 'New',
        '{date_added}'   => date('Y-m-d')
    ];
    foreach ($replacements as $var => $val) {
        $body = str_replace($var, $val, $body);
        $subject = str_replace($var, $val, $subject);
    }

    $body = wpautop($body);     

    add_action('phpmailer_init', 'mq_configure_phpmailer');

    $headers = ['Content-Type: text/html; charset=UTF-8'];

    $sent = wp_mail($to, $subject, $body, $headers);

    if (!$sent) {
        error_log('MQ Test Email failed to send to ' . $to);
        wp_safe_redirect(admin_url('admin.php?page=mq-templates&mode=edit&id=' . $id . '&test_fail=1'));
    }

    remove_action('phpmailer_init', 'mq_configure_phpmailer');

    wp_safe_redirect(admin_url('admin.php?page=mq-templates&mode=edit&id=' . $id . '&test_sent=1'));
    exit;
}


/******************************************************************************************/
/***********************************Add & Edit Templates***********************************/
/******************************************************************************************/
add_action('admin_post_mq_save_template', 'mq_save_template_handler');
function mq_save_template_handler() {
    if (!current_user_can('manage_options')) wp_die('Unauthorized');

    if (!isset($_POST['mq_template_nonce']) || !wp_verify_nonce($_POST['mq_template_nonce'], 'mq_save_template')) {
        wp_die('Invalid nonce');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'mq_email_templates';

    $id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
    $title = sanitize_text_field($_POST['template_title']);
    $subject = sanitize_text_field($_POST['template_subject']);
    $body = wp_kses_post($_POST['template_body']);

    if ($id) {
        $wpdb->update(
            $table_name,
            ['template_name' => $title, 'subject' => $subject, 'body' => $body],
            ['id' => $id],
            ['%s','%s','%s'],
            ['%d']
        );
        $redirect_url = admin_url('admin.php?page=mq-templates&mode=edit&id=' . $id . '&updated=1');
    } else {
        $wpdb->insert(
            $table_name,
            ['template_name' => $title, 'subject' => $subject, 'body' => $body],
            ['%s','%s','%s']
        );
        $id = $wpdb->insert_id;
        $redirect_url = admin_url('admin.php?page=mq-templates&mode=edit&id=' . $id . '&added=1');
    }

    wp_safe_redirect($redirect_url);
    exit;
}


/******************************************************************************************/
/***********************************Bulk Email Sender**************************************/
/******************************************************************************************/
add_action('wp_ajax_mq_send_emails', 'mq_send_emails_handler');

function mq_send_emails_handler() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized.']);
    }

    global $wpdb;
    $table_leads     = $wpdb->prefix . 'mq_leads';
    $table_templates = $wpdb->prefix . 'mq_email_templates';
    $table_logs      = $wpdb->prefix . 'mq_email_logs';

    $template_id = intval($_POST['template_id'] ?? 0);
    $lead_ids    = isset($_POST['lead_ids']) ? array_map('intval', $_POST['lead_ids']) : [];

    if (empty($template_id) || empty($lead_ids)) {
        wp_send_json_error(['message' => 'Template or leads not selected.']);
    }

    $template = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_templates WHERE id = %d", $template_id),
        ARRAY_A
    );
    if (!$template) {
        wp_send_json_error(['message' => 'Template not found.']);
    }

    $placeholders = implode(',', array_fill(0, count($lead_ids), '%d'));
    $query = $wpdb->prepare(
        "SELECT * FROM $table_leads WHERE id IN ($placeholders) AND email IS NOT NULL AND email != ''",
        $lead_ids
    );
    $leads = $wpdb->get_results($query, ARRAY_A);
    if (empty($leads)) {
        wp_send_json_error(['message' => 'No valid leads with emails.']);
    }

    add_action('phpmailer_init', 'mq_configure_phpmailer');

    $sent = 0;

    foreach ($leads as $lead) {
        $to      = $lead['email'];
        $subject = $template['subject'];
        $body    = $template['body'];

        foreach ($lead as $key => $value) {
            $body = str_replace("{{$key}}", $value, $body);
            $subject = str_replace("{{$key}}", $value, $subject);
        }

        // Insert log first
        $wpdb->insert(
            $table_logs,
            [
                'lead_id'      => $lead['id'],
                'template_id'  => $template['id'],
                'recipient'    => $to,
                'subject'      => $subject,
                'body'         => $body,
                'status'       => 'pending',
                'error_message'=> null,
                'sent_at'      => current_time('mysql')
            ],
            ['%d','%d','%s','%s','%s','%s','%s','%s']
        );

        $log_id = $wpdb->insert_id;

        // Append unique tracking pixel
        $tracking_url = add_query_arg('mq_email_track', $log_id, site_url('/'));
        $body = wpautop($body);     
        $body .= '<img src="' . esc_url($tracking_url) . '" width="1" height="1" style="display:none;" />';

        $headers = ['Content-Type: text/html; charset=UTF-8'];

        $wpdb->update(  
            $table_logs,
            ['body' => $body],
            ['id' => $log_id],
            ['%s'],
            ['%d']
        );

        if (wp_mail($to, $subject, $body, $headers)) {
            $sent++;
            $wpdb->update($table_logs, ['status' => 'sent'], ['id' => $log_id], ['%s'], ['%d']);
            $wpdb->update($table_leads, ['status' => 'sent'], ['id' => $lead['id']], ['%s'], ['%d']);
        } else {
            $wpdb->update(
                $table_logs,
                ['status' => 'failed', 'error_message' => 'Email sending failed'],
                ['id' => $log_id],
                ['%s','%s'],
                ['%d']
            );
        }
    }

    remove_action('phpmailer_init', 'mq_configure_phpmailer');

    wp_send_json_success(['message' => $sent]);
}


/******************************************************************************************/
/***********************************Cron for Emails****************************************/
/******************************************************************************************/
add_action('admin_init', 'mq_save_cron_settings');
function mq_save_cron_settings() {
    if (!isset($_POST['mq_settings_nonce']) || !wp_verify_nonce($_POST['mq_settings_nonce'], 'mq_save_settings')) {
        return;
    }

    if (isset($_POST['mq_cron_template'])) {
        update_option('mq_cron_template', intval($_POST['mq_cron_template']));
    }

    if (isset($_POST['mq_cron_interval'])) {
        $interval = max(1, intval($_POST['mq_cron_interval']));
        update_option('mq_cron_interval', $interval);

        wp_clear_scheduled_hook('mq_cron_send_emails');

        if (!wp_next_scheduled('mq_cron_send_emails')) {
            wp_schedule_event(time() + 60, 'mq_custom_interval', 'mq_cron_send_emails');
        }
    }
}

add_filter('cron_schedules', 'mq_add_custom_cron_interval');
function mq_add_custom_cron_interval($schedules) {
    $interval = get_option('mq_cron_interval', 5);
    $schedules['mq_custom_interval'] = [
        'interval' => $interval * 60,
        'display'  => "Every {$interval} Minutes"
    ];
    return $schedules;
}


add_action('mq_cron_send_emails', 'mq_process_cron_emails');

function mq_process_cron_emails() {
    global $wpdb;
    $table_leads     = $wpdb->prefix . 'mq_leads';
    $table_templates = $wpdb->prefix . 'mq_email_templates';
    $table_logs      = $wpdb->prefix . 'mq_email_logs';

    $template_id = intval(get_option('mq_cron_template', 0));
    if (!$template_id) return;

    $lead = $wpdb->get_row(
        "SELECT * FROM $table_leads WHERE status = 'New' AND email IS NOT NULL AND email != '' ORDER BY RAND() LIMIT 1",
        ARRAY_A
    );
    if (!$lead) return;

    $template = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_templates WHERE id = %d", $template_id),
        ARRAY_A
    );
    if (!$template) return;

    $to      = $lead['email'];
    $subject = $template['subject'];
    $body    = $template['body'];

    foreach ($lead as $key => $value) {
        $body    = str_replace("{{$key}}", $value, $body);
        $subject = str_replace("{{$key}}", $value, $subject);
    }
    $body = wpautop($body);     


    $log_data = [
        'lead_id'      => $lead['id'],
        'template_id'  => $template['id'],
        'recipient'    => $to,
        'subject'      => $subject,
        'body'         => $body,
        'status'       => 'sent',
        'error_message'=> null,
        'sent_at'      => current_time('mysql')
    ];

    $wpdb->insert(
        $table_logs,
        $log_data,
        ['%d','%d','%s','%s','%s','%s','%s','%s']
    );

    $log_id = $wpdb->insert_id;
    $tracking_url = add_query_arg('mq_email_track', $log_id, site_url('/'));
    $body .= '<img src="' . esc_url($tracking_url) . '" width="1" height="1" style="display:none;" />';

    $headers = ['Content-Type: text/html; charset=UTF-8'];
    add_action('phpmailer_init', 'mq_configure_phpmailer');
    $sent = wp_mail($to, $subject, $body, $headers);
    remove_action('phpmailer_init', 'mq_configure_phpmailer');

    $wpdb->update(
        $table_logs,
        ['body' => $body],
        ['id' => $log_id],
        ['%s'],
        ['%d']
    );

    if ($sent) {
        $wpdb->update(
            $table_leads,
            ['status' => 'sent'], 
            ['id' => $lead['id']],
            ['%s'],
            ['%d']
        );
    } else {
        $wpdb->update(
            $table_logs,
            ['status' => 'failed', 'error_message' => 'Email sending failed'],
            ['id' => $log_id],
            ['%s','%s'],
            ['%d']
        );
    }
}

add_action('init', function() {
    if (isset($_GET['mq_email_track'])) {
        global $wpdb;
        $table_logs = $wpdb->prefix . 'mq_email_logs';
        $log_id = intval($_GET['mq_email_track']);
        if ($log_id) {
            $wpdb->update(
                $table_logs,
                ['status' => 'opened'],
                ['id' => $log_id],
                ['%s'],
                ['%d']
            );
        }
        header('Content-Type: image/gif');
        echo base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='); 
        exit;
    }
});



/******************************************************************************************/
/********************************Bounced or Responsed**************************************/
/******************************************************************************************/

// function mq_check_email_responses() {
//     global $wpdb;
//     $table_logs = $wpdb->prefix . 'mq_email_logs';

//     $smtp_user = get_option('mq_smtp_user', '');
//     $smtp_pass = get_option('mq_smtp_pass', '');

//     if (empty($smtp_user) || empty($smtp_pass)) return;

//     $imap_server = '{imap.gmail.com:993/imap/ssl}INBOX';
//     $imap = @imap_open($imap_server, $smtp_user, $smtp_pass);

//     if (!$imap) {
//         error_log('IMAP Connection failed: ' . imap_last_error());
//         return;
//     }
//     $emails = imap_search($imap, 'UNSEEN');

//     if ($emails) {
//         foreach ($emails as $email_id) {
//             $overview = imap_fetch_overview($imap, $email_id, 0)[0];
//             $body     = imap_fetchbody($imap, $email_id, 1);
//             $updated  = false;

//             $recipient = '';
//             if (preg_match('/Original-Recipient: rfc822;(.*)/i', $body, $matches)) {
//                 $recipient = trim($matches[1]);
//             } elseif (preg_match('/Your message wasn\'t delivered to (.+?) because/i', $body, $matches)) {
//                 $recipient = trim($matches[1]);
//             }

//             if (!empty($recipient)) {
//                 $wpdb->update(
//                     $table_logs,
//                     ['status' => 'bounced'],
//                     ['recipient' => $recipient],
//                     ['%s'],
//                     ['%s']
//                 );
//                 $updated = true;
//             }
//             if (!$updated && !empty($overview->from)) {
//                 if (preg_match('/<(.+?)>/', $overview->from, $matches)) {
//                     $sender = trim($matches[1]);
//                     $wpdb->update(
//                         $table_logs,
//                         ['status' => 'responded'],
//                         ['recipient' => $sender],
//                         ['%s'],
//                         ['%s']
//                     );
//                 }
//             }

//             imap_setflag_full($imap, $email_id, "\\Seen");
//         }
//     }

//     imap_close($imap);
// }

// add_action('mq_cron_check_responses', 'mq_check_email_responses');
// add_action('init', 'mq_check_email_responses');

// if (!wp_next_scheduled('mq_cron_check_responses')) {
//     wp_schedule_event(time(), '1min', 'mq_cron_check_responses');
// }
