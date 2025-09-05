<?php

if(!function_exists('mqat_render_admin_page')){
    function mqat_render_admin_page() {
        if (!current_user_can('manage_options')) return;

        global $wpdb;

        // Actions: export CSV or clear logs
        if (isset($_GET['export']) && $_GET['export'] === 'csv' && check_admin_referer('mqat_export')) {
            mqat_export_csv();
            exit;
        }
        if (isset($_POST['mqat_clear']) && check_admin_referer('mqat_clear')) {
            $wpdb->query("TRUNCATE TABLE " . MQAL_TABLE);
            echo '<div class="notice notice-success"><p>Activity log cleared.</p></div>';
        }

        // Filters
        $action   = isset($_GET['act']) ? sanitize_key($_GET['act']) : '';
        $s        = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $paged    = max(1, (int)($_GET['paged'] ?? 1));
        $per_page = 50;
        $offset   = ($paged - 1) * $per_page;

        $where = 'WHERE 1=1';
        $args  = [];

        if ($action !== '') {
            $where .= " AND action = %s";
            $args[] = $action;
        }
        if ($s !== '') {
            $like = '%' . $wpdb->esc_like($s) . '%';
            $where .= " AND (message LIKE %s OR object_type LIKE %s OR ip LIKE %s)";
            $args[] = $like; $args[] = $like; $args[] = $like;
        }

        $total = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . MQAL_TABLE . " $where", $args));

        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM " . MQAL_TABLE . " $where ORDER BY logged_at DESC, id DESC LIMIT %d OFFSET %d",
            array_merge($args, [$per_page, $offset])
        ));

        $last_actions = $wpdb->get_col("SELECT DISTINCT action FROM " . MQAL_TABLE . " ORDER BY action ASC LIMIT 100");

        // Build URLs
        $base_url = menu_page_url('mq-activity-log', false);
        $qs = function(array $extra=[]) use ($base_url, $action, $s) {
            $params = array_filter(array_merge(['page'=>'mq-activity-log', 'act'=>$action, 's'=>$s], $extra), function($v){ return $v !== '' && $v !== null; });
            return esc_url(add_query_arg($params, admin_url('admin.php')));
        };

        $total_pages = max(1, (int)ceil($total / $per_page));
        ?>
        <div class="wrap">
            <h1>MQ Activity Log</h1>

            <form method="get" style="margin:12px 0;">
                <input type="hidden" name="page" value="mq-activity-log" />
                <select name="act">
                    <option value="">All actions</option>
                    <?php foreach ($last_actions as $a): ?>
                        <option value="<?php echo esc_attr($a); ?>" <?php selected($action, $a); ?>><?php echo esc_html($a); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="search" name="s" value="<?php echo esc_attr($s); ?>" placeholder="Search message/IP/type" />
                <button class="button">Filter</button>
                <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=mq-activity-log')); ?>">Reset</a>
                &nbsp;&nbsp;
                <a class="button button-secondary"
                    href="<?php echo wp_nonce_url( admin_url('admin-post.php?action=mqat_export'), 'mqat_export' ); ?>">
                    Export CSV
                </a>

            </form>

            <form method="post" onsubmit="return confirm('Clear all activity logs?');" style="margin-bottom:12px;">
                <?php wp_nonce_field('mqat_clear'); ?>
                <button class="button button-danger">Clear Logs</button>
                <input type="hidden" name="mqat_clear" value="1" />
            </form>

            <div class="tablenav top" style="margin:10px 0;">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo number_format_i18n($total); ?> items</span>
                    <?php if ($total_pages > 1): ?>
                        <span class="pagination-links">
                            <a class="first-page button" href="<?php echo $qs(['paged'=>1]); ?>">&laquo;</a>
                            <a class="prev-page button" href="<?php echo $qs(['paged'=>max(1,$paged-1)]); ?>">&lsaquo;</a>
                            <span class="paging-input"><?php echo esc_html($paged); ?> of <span class="total-pages"><?php echo esc_html($total_pages); ?></span></span>
                            <a class="next-page button" href="<?php echo $qs(['paged'=>min($total_pages,$paged+1)]); ?>">&rsaquo;</a>
                            <a class="last-page button" href="<?php echo $qs(['paged'=>$total_pages]); ?>">&raquo;</a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width:160px;">Time</th>
                        <th style="width:80px;">User</th>
                        <th style="width:120px;">IP</th>
                        <th style="width:140px;">Action</th>
                        <th style="width:140px;">Object</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="6">No activity yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?php echo esc_html(mysql2date('Y-m-d H:i:s', $r->logged_at)); ?></td>
                            <td>
                                <?php
                                if ($r->user_id) {
                                    $u = get_userdata($r->user_id);
                                    echo $u ? '<a href="' . esc_url(admin_url('user-edit.php?user_id=' . $r->user_id)) . '">' . esc_html($u->user_login) . '</a>' : (int)$r->user_id;
                                } else {
                                    echo '—';
                                }
                                ?>
                            </td>
                            <td><?php echo esc_html($r->ip ?: ''); ?></td>
                            <td><code><?php echo esc_html($r->action); ?></code></td>
                            <td><?php echo esc_html($r->object_type ?: '') . ($r->object_id ? ' #'.(int)$r->object_id : ''); ?></td>
                            <td><?php echo wp_kses_post($r->message); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>

            <div class="tablenav bottom" style="margin:10px 0;">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo number_format_i18n($total); ?> items</span>
                    <?php if ($total_pages > 1): ?>
                        <span class="pagination-links">
                            <a class="first-page button" href="<?php echo $qs(['paged'=>1]); ?>">&laquo;</a>
                            <a class="prev-page button" href="<?php echo $qs(['paged'=>max(1,$paged-1)]); ?>">&lsaquo;</a>
                            <span class="paging-input"><?php echo esc_html($paged); ?> of <span class="total-pages"><?php echo esc_html($total_pages); ?></span></span>
                            <a class="next-page button" href="<?php echo $qs(['paged'=>min($total_pages,$paged+1)]); ?>">&rsaquo;</a>
                            <a class="last-page button" href="<?php echo $qs(['paged'=>$total_pages]); ?>">&raquo;</a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}

