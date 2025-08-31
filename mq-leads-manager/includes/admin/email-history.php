<?php
function mq_email_page() {
    global $wpdb;
    $email_logs_table = $wpdb->prefix . 'mq_email_logs';

    
    $results = $wpdb->get_results("SELECT * FROM $email_logs_table ORDER BY id DESC", ARRAY_A);
    $statuses = $wpdb->get_col("SELECT DISTINCT status FROM $email_logs_table ORDER BY status ASC");


    ?>
    <div class="wrap">
        <h1>MQ Email History</h1>

        <div id="emails" class="tab-content">
            <div class="mq-top-bar">
                <div id="mq-filters">
                    <input type="number" id="search-id" placeholder="Search Email ID">
                    <input type="number" id="search-lead-id" placeholder="Search Lead ID">
                    <input type="number" id="search-template-id" placeholder="Search Template ID">
                    <input type="text" id="search-recipient" placeholder="Search Recipient">

                    <select id="search-status">
                        <option value="">All Status</option>
                        <?php foreach($statuses as $s) echo "<option value='".esc_attr($s)."'>".esc_html($s)."</option>"; ?>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:10px;">
                <label for="mq-show-entries">Show:</label>
                <select id="mq-show-entries">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select> entries
            </div>

            <table id="mq-emails-table" class="widefat striped" style="width:100%;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Lead ID</th>
                        <th>Template ID</th>
                        <th>Recipient</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                    <tr data-id="<?php echo $row['id']; ?>">
                        <td><?php echo esc_html($row['id']); ?></td>
                        <td><?php echo esc_html($row['lead_id']); ?></td>
                        <td><?php echo esc_html($row['template_id']); ?></td>
                        <td><?php echo esc_html($row['recipient']); ?></td>
                        <td class="status"><?php echo esc_html($row['status']); ?></td>
                        <td><?php echo esc_html($row['sent_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php
}
