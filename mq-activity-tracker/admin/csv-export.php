<?php
/**
 * MQ Activity Log – CSV Export
 */

if ( ! defined('ABSPATH') ) exit;

/**
 * Export Activity Log to CSV
 */
function mqat_export_csv() {
    if ( ! current_user_can('manage_options') ) {
        wp_die('Unauthorized');
    }
    check_admin_referer('mqat_export');

    global $wpdb;
    $table = $wpdb->prefix . 'mq_activity_log';

    $rows = $wpdb->get_results("
        SELECT id, logged_at, user_id, ip, action, object_type, object_id, message
        FROM {$table}
        ORDER BY logged_at DESC, id DESC
        LIMIT 5000
    ", ARRAY_A);

    while ( ob_get_level() ) {
        ob_end_clean();
    }

    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=activity-log-' . date('Ymd-His') . '.csv');

    $out = fopen('php://output', 'w');

    fputcsv($out, ['id','logged_at','user_id','ip','action','object_type','object_id','message']);

    if ( $rows ) {
        foreach ( $rows as $r ) {
            fputcsv($out, [
                $r['id'],
                $r['logged_at'],
                $r['user_id'],
                $r['ip'],
                $r['action'],
                $r['object_type'],
                $r['object_id'],
                wp_strip_all_tags($r['message']),
            ]);
        }
    }

    fclose($out);
    exit;
}

/**
 * Register the export action
 */
add_action('admin_post_mqat_export', 'mqat_export_csv');
