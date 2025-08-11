<?php

class Database {
    public function challan_search_create() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'challan_data';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            issue_date DATE NOT NULL,
            due_date DATE NOT NULL,
            last_date DATE NOT NULL DEFAULT CURRENT_DATE,
            enrollment_no VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            class VARCHAR(255) NOT NULL,
            section VARCHAR(255) NOT NULL,
            roll_no VARCHAR(255) NOT NULL,
            admission_fee DECIMAL(10, 2) NOT NULL,
            security_fee DECIMAL(10, 2) NOT NULL,
            tuition_fee DECIMAL(10, 2) NOT NULL,
            computer_fee DECIMAL(10, 2) NOT NULL,
            advance_fee DECIMAL(10, 2) NOT NULL,
            arrears DECIMAL(10, 2) NOT NULL,
            fines DECIMAL(10, 2) NOT NULL,
            due_date_fine DECIMAL(10, 2) NOT NULL,
            others DECIMAL(10, 2) NOT NULL,
            other_details TEXT,
            remarks TEXT,
            father_name VARCHAR(255) NOT NULL,
            exam_lab_fee DECIMAL(10, 2) NOT NULL,
            development_fee DECIMAL(10, 2) NOT NULL,
            house VARCHAR(255) NOT NULL,
            pdf_link TEXT,
            status ENUM('Unpaid', 'Paid', 'Overdue') DEFAULT 'Unpaid' NOT NULL
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        if ($wpdb->last_error) {
            error_log('Database error: ' . $wpdb->last_error);
        }
    }

    public function challan_search_remove() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'challan_data';
        $sql = "DROP TABLE IF EXISTS $table_name;";
        $wpdb->query($sql);

        if ($wpdb->last_error) {
            error_log('Database error: ' . $wpdb->last_error);
        }
    }
}

?>
