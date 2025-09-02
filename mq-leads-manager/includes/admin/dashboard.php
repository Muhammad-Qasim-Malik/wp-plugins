<?php
function mq_dashboard_page() {
    global $wpdb;

    // Tables
    $email_logs_table = $wpdb->prefix . 'mq_email_logs';
    $leads_table      = $wpdb->prefix . 'mq_leads';
    $templates_table  = $wpdb->prefix . 'mq_email_templates';

    // Email logs
    $results = $wpdb->get_results("SELECT * FROM $email_logs_table ORDER BY id DESC", ARRAY_A);
    $statuses = $wpdb->get_col("SELECT DISTINCT status FROM $email_logs_table ORDER BY status ASC");

    // Stats
    $total_leads      = (int) $wpdb->get_var("SELECT COUNT(*) FROM $leads_table");
    $leads_with_email = (int) $wpdb->get_var("SELECT COUNT(*) FROM $leads_table WHERE email != ''");
    $emails_sent      = (int) $wpdb->get_var("SELECT COUNT(*) FROM $email_logs_table WHERE status = 'sent'");
    $emails_opened    = (int) $wpdb->get_var("SELECT COUNT(*) FROM $email_logs_table WHERE status = 'opened'");
    $total_templates  = (int) $wpdb->get_var("SELECT COUNT(*) FROM $templates_table");

    ?>
    <div class="wrap">
        <h1>MQ Leads Manager</h1>

        <div id="stats" class="tab-content" >
            <h2>Analytics</h2>
            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                <div class="card" style="background:#f1f1f1;padding:20px;border-radius:8px;flex:1;">
                    <h3>Total Leads</h3>
                    <p><?php echo $total_leads; ?></p>
                </div>
                <div class="card" style="background:#f1f1f1;padding:20px;border-radius:8px;flex:1;">
                    <h3>Leads with Email</h3>
                    <p><?php echo $leads_with_email; ?></p>
                </div>
                <div class="card" style="background:#f1f1f1;padding:20px;border-radius:8px;flex:1;">
                    <h3>Emails Sent</h3>
                    <p><?php echo $emails_sent; ?></p>
                </div>
                <div class="card" style="background:#f1f1f1;padding:20px;border-radius:8px;flex:1;">
                    <h3>Emails Opened</h3>
                    <p><?php echo $emails_opened; ?></p>
                </div>
                <div class="card" style="background:#f1f1f1;padding:20px;border-radius:8px;flex:1;">
                    <h3>Total Templates</h3>
                    <p><?php echo $total_templates; ?></p>
                </div>
            </div>

            <canvas id="mq-stats-chart" style="margin-top:30px;max-width:800px;"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

    const ctx = document.getElementById('mq-stats-chart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Total Leads', 'Leads w/ Email', 'Emails Sent', 'Emails Opened', 'Templates'],
            datasets: [{
                label: 'Count',
                data: [
                    <?php echo $total_leads; ?>,
                    <?php echo $leads_with_email; ?>,
                    <?php echo $emails_sent; ?>,
                    <?php echo $emails_opened; ?>,
                    <?php echo $total_templates; ?>
                ],
                backgroundColor: ['#0073aa','#46b450','#dc3232','#ffb900','#23282d']
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
    </script>
<?php
}
