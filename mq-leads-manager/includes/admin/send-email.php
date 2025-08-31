<?php

function mq_send_email_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mq_leads';
    $templates_table = $wpdb->prefix . 'mq_email_templates';
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A);

    $niches = $wpdb->get_col("SELECT DISTINCT niche FROM $table_name WHERE email IS NOT NULL AND email != '' ORDER BY niche ASC");
    $cities = $wpdb->get_col("SELECT DISTINCT city FROM $table_name WHERE email IS NOT NULL AND email != '' ORDER BY city ASC");
    $statuses = $wpdb->get_col("SELECT DISTINCT status FROM $table_name WHERE email IS NOT NULL AND email != '' ORDER BY status ASC");
    $city_country_map = [];
    $rows = $wpdb->get_results("SELECT DISTINCT city, country FROM $table_name", ARRAY_A);
    

    $templates = $wpdb->get_results("
        SELECT id, template_name, subject 
        FROM $templates_table 
        ORDER BY id DESC
    ", ARRAY_A);

    if (isset($_GET['sent'])) {
        echo '<div class="notice notice-success is-dismissible"><p>Email sent to ' . intval($_GET['sent']) . ' persons successfully!</p></div>';
    }

    foreach ($rows as $row) $city_country_map[$row['city']] = $row['country'];
    ?>
    <div class="wrap">
        <h1>Manage Leads</h1>

        <div class="mq-top-bar">
            <div id="mq-filters">
                <input type="number" id="search-id" placeholder="Search ID">
                <input type="text" id="search-name" placeholder="Search Name">
                <input type="text" id="search-email" placeholder="Search Email">
                <input type="text" id="search-phone" placeholder="Search Phone">
                <input type="text" id="search-website" placeholder="Search Website">
                <input type="text" id="search-address" placeholder="Search Address">

                <select id="search-niche">
                    <option value="">All Niche</option>
                    <?php foreach($niches as $n) echo "<option value='".esc_attr($n)."'>".esc_html($n)."</option>"; ?>
                </select>

                <select id="search-city">
                    <option value="">All City</option>
                    <?php foreach($cities as $c) echo "<option value='".esc_attr($c)."'>".esc_html($c)."</option>"; ?>
                </select>

                <input type="text" id="search-country" placeholder="Country" readonly>

                <select id="search-status">
                    <option value="">All Status</option>
                    <?php foreach($statuses as $s) echo "<option value='".esc_attr($s)."'>".esc_html($s)."</option>"; ?>
                </select>
            </div>

            <div>
                <button id="mq-template-btn" class="button button-primary">Send Message</button>
            </div>
        </div>

        <div id="mq-template-popup" class="mq-modal">
            <div class="mq-popup-content">
                <span id="mq-close-template-popup" class="mq-close">&times;</span>
                <h2>Select Template</h2>
                
                <select id="mq-template-select">
                    <option value="">-- Select a Template --</option>
                    <?php foreach ($templates as $t): ?>
                        <option value="<?php echo esc_attr($t['id']); ?>"
                                data-template-name="<?php echo esc_attr($t['template_name']); ?>"
                                data-subject="<?php echo esc_attr($t['subject']); ?>">
                            <?php echo esc_html($t['template_name']); ?> (<?php echo esc_html($t['subject']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <div style="margin-top:15px;">
                    <button id="mq-send-email-btn" class="button button-primary">Send Emails</button>
                </div>
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

        <table id="mq-leads-table" class="widefat striped" style="width:100%;">
            <thead>
                <tr>
                    <th><input type="checkbox" id="mq-select-all-email"></th>
                    <th>ID</th>
                    <th>Business Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Website</th>
                    <th>Niche</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                <tr data-id="<?php echo $row['id']; ?>">
                    <td><input type="checkbox" class="mq-send-checkbox" value="<?php echo $row['id']; ?>"></td>
                    <td><?php echo esc_html($row['id']); ?></td>
                    <td><?php echo esc_html($row['business_name']); ?></td>
                    <td><?php echo esc_html($row['email']); ?></td>
                    <td><?php echo esc_html($row['phone']); ?></td>
                    <td><?php echo esc_html($row['website']); ?></td>
                    <td><?php echo esc_html($row['niche']); ?></td>
                    <td><?php echo esc_html($row['address']); ?></td>
                    <td><?php echo esc_html($row['city']); ?></td>
                    <td><?php echo esc_html($row['country']); ?></td>
                    <td class="status"><?php echo esc_html($row['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        var cityCountryMap = <?php echo json_encode($city_country_map); ?>;
    </script>
<?php
}
