<?php

function mq_leads_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mq_leads';
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A);

    $niches = $wpdb->get_col("SELECT DISTINCT niche FROM $table_name ORDER BY niche ASC");
    $cities = $wpdb->get_col("SELECT DISTINCT city FROM $table_name ORDER BY city ASC");
    $statuses = $wpdb->get_col("SELECT DISTINCT status FROM $table_name ORDER BY status ASC");
    $city_country_map = [];
    $rows = $wpdb->get_results("SELECT DISTINCT city, country FROM $table_name", ARRAY_A);
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
                <button id="mq-import-btn" class="button button-primary">Import Leads</button>
            </div>
        </div>
        <div id="mq-import-modal" class="mq-modal">
            <h2>Import Leads</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="mq_import_file" accept=".csv" required>
                <input type="submit" name="mq_import_submit" class="button button-primary" value="Upload">
            </form>
            <button id="mq-close-modal" class="button">Close</button>
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

        <div>
            <button id="mq-bulk-update-status" class="button button-secondary">Update Status</button>
            <button id="mq-bulk-delete" class="button button-secondary">Delete Selected</button>
        </div>

        <table id="mq-leads-table" class="widefat striped" style="width:100%;">
            <thead>
                <tr>
                    <th><input type="checkbox" id="mq-select-all"></th>
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                <tr data-id="<?php echo $row['id']; ?>">
                    <td><input type="checkbox" class="mq-lead-checkbox" value="<?php echo $row['id']; ?>"></td>
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
                    <td>
                        <button class="mq-edit-status button button-small">Edit</button>
                        <button class="mq-delete-lead button button-small">Delete</button>
                    </td>
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
