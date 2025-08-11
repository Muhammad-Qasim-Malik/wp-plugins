<?php
require_once plugin_dir_path(__FILE__) . '../libs/pdf.php';
require_once plugin_dir_path(__FILE__) . '../libs/SimpleXLSX.php';
require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';
    
use PhpOffice\PhpSpreadsheet\Shared\Date; 

function challan_add_new_member($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'challan_data';

    // Extract year and month from due_date
    $due_date = $data['due_date'];
    $due_date_timestamp = strtotime($due_date);
    $year = date('Y', $due_date_timestamp);
    $month = date('m', $due_date_timestamp);

    // Set last_date to the 25th of the due_date month
    $last_date = date('Y-m-d', strtotime("$year-$month-25"));

    $wpdb->insert($table_name, [
        'issue_date' => $data['issue_date'],
        'due_date' => $data['due_date'],
        'last_date' => $last_date, 
        'enrollment_no' => $data['enrollment_no'],
        'name' => $data['name'],
        'class' => $data['class'],
        'section' => $data['section'],
        'roll_no' => $data['roll_no'],
        'admission_fee' => (float)$data['admission_fee'],
        'security_fee' => (float)$data['security_fee'],
        'tuition_fee' => (float)$data['tuition_fee'],
        'computer_fee' => (float)$data['computer_fee'],
        'advance_fee' => (float)$data['advance_fee'],
        'arrears' => (float)$data['arrears'],
        'fines' => (float)$data['fines'],
        'due_date_fine' => (float)$data['due_date_fine'],
        'others' => (float)$data['others'],
        'other_details' => $data['other_details'],
        'remarks' => $data['remarks'],
        'admission_date' => $data['admission_date'],
        'father_name' => $data['father_name'],
        'exam_lab_fee' => (float)$data['exam_lab_fee'],
        'development_fee' => (float)$data['development_fee'],
        'house' => $data['house'],
        'status' => 'Unpaid'
    ]);

    return $wpdb->insert_id;
}


function challan_add_form() {
    update_challan_status();
    if (isset($_POST['add_challan'])) {

        $data = [];
        foreach ($_POST as $key => $value) {
            if ($key !== 'add_challan') {
                $data[$key] = in_array($key, ['admission_fee', 'security_fee', 'tuition_fee', 'computer_fee', 'advance_fee', 'arrears', 'fines', 'due_date_fine', 'others', 'exam_lab_fee', 'development_fee']) ? (float)$value : sanitize_text_field($value);
            }
        }
        
        if (strlen($data['enrollment_no']) !== 4 || !ctype_digit($data['enrollment_no'])) {
            echo '<div class="notice notice-error"><p>Enrollment number must be exactly 4 digits.</p></div>';
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . 'challan_data';
            challan_add_new_member($data);
            $pdf_path = challan_generate_pdf($data);
            $wpdb->update($table_name, ['pdf_link' => $pdf_path], ['id' => $wpdb->insert_id]);
            echo '<div class="notice notice-success"><p>New member added successfully.</p></div>';
        }
    }
    
    echo '<div class="challan-form-admin">';
    echo '<h2>Add New Challan</h2>';
    echo '<form method="POST">';
    
    $fields = [
        'issue_date' => 'Issue Date',
        'due_date' => 'Due Date',
        'enrollment_no' => 'Enrollment No',
        'name' => 'Name',
        'class' => 'Class',
        'section' => 'Section',
        'roll_no' => 'Roll No',
        'admission_fee' => 'Admission Fee',
        'security_fee' => 'Security Fee',
        'tuition_fee' => 'Tuition Fee',
        'computer_fee' => 'Computer Fee',
        'advance_fee' => 'Advance Fee',
        'arrears' => 'Arrears',
        'fines' => 'Fines',
        'due_date_fine' => 'Due Date Fine',
        'others' => 'Other Fees',
        'other_details' => 'Other Details',
        'remarks' => 'Remarks',
        'admission_date' => 'Admission Date',
        'father_name' => 'Father Name',
        'exam_lab_fee' => 'Exam Lab Fee',
        'development_fee' => 'Development Fee',
        'house' => 'House'
    ];
    
    foreach ($fields as $name => $label) {
        $input_type = in_array($name, ['issue_date', 'due_date', 'admission_date']) ? 'date' : (in_array($name, ['admission_fee', 'security_fee', 'tuition_fee', 'computer_fee', 'advance_fee', 'arrears', 'fines', 'due_date_fine', 'others', 'exam_lab_fee', 'development_fee']) ? 'number' : 'text');
        $extra_attr = ($name === 'enrollment_no') ? "minlength='4' maxlength='4' title='Must be exactly 4 digits'" : "";
        echo "<label>{$label}:</label><input type='{$input_type}' step='0.01' name='{$name}' required {$extra_attr}><br>";
    }
    
    echo '<input type="submit" name="add_challan" value="Add" class="button button-primary">';
    echo '</form>';
    echo '</div>';
}

function challan_upload_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['upload_challan'])) {
        if (!empty($_FILES['challan_file']['tmp_name'])) {
            challan_process_upload($_FILES['challan_file']['tmp_name']);
            echo '<div class="notice notice-success"><p>File uploaded and data inserted successfully.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Please upload a valid file.</p></div>';
        }
    }

    echo '<div class="wrap" style="display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column;">';
    echo '<div style="text-align: center; margin-bottom: 20px;">';
    echo '<h1><span class="dashicons dashicons-upload" style="font-size: 30px; margin-right: 10px;"></span>Upload Challan Data</h1>';
    echo '</div>';
    echo '<div class="challan-upload-form" style="background: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); padding: 30px; width: 100%; max-width: 500px;">';
    
    echo '<form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">';
    echo '<div style="display: flex; flex-direction: column;">';
    echo '<label for="challan_file" style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">Choose Challan File (.xlsx)</label>';
    echo '<input type="file" name="challan_file" accept=".xlsx" required style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">';
    echo '</div>';
    echo '<input type="submit" name="upload_challan" value="Upload" class="button button-primary" style="background-color: #0073aa; border: none; color: white; padding: 12px 20px; font-size: 16px; cursor: pointer; border-radius: 4px;">';
    echo '</form>';
    
    echo '</div>';
    echo '</div>';
}

function challan_process_upload($file_path) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'challan_data';

    $xlsx = SimpleXLSX::parse($file_path);

    if ($xlsx) {
        foreach ($xlsx as $index => $row) {
            if ($index === 0) { 
                continue; 
            }

            $due_date = Date::excelToDateTimeObject($row[1])->format('Y-m-d');
            $due_date_timestamp = strtotime($due_date);
            $year = date('Y', $due_date_timestamp);
            $month = date('m', $due_date_timestamp);

            // Set last_date to the 25th of the due_date month
            $last_date = date('Y-m-d', strtotime("$year-$month-25"));
    
            $data = [
                'issue_date'     => Date::excelToDateTimeObject($row[0])->format('Y-m-d'), 
                'due_date'      => Date::excelToDateTimeObject($row[1])->format('Y-m-d'),
                'last_date'     => $last_date,
                'enrollment_no'   =>(int)$row[2] ?? '-',
                'name'            => $row[3] ?? '-',
                'class' => isset($row[4]) ? preg_replace('/^\d+-/', '', $row[4]) : '-',
                'section'         => $row[5] ?? '-',
                'roll_no'         => (int)$row[6] ?? '-',
                'admission_fee'   => floatval($row[7] ?? 0),
                'security_fee'    => floatval($row[8] ?? 0),
                'tuition_fee'     => floatval($row[9] ?? 0),
                'computer_fee'    => floatval($row[10] ?? 0),
                'advance_fee'     => floatval($row[11] ?? 0),
                'arrears'         => floatval($row[12] ?? 0),
                'fines'           => floatval($row[13] ?? 0),
                'due_date_fine'   => floatval($row[14] ?? 0),
                'others'          => floatval($row[15] ?? 0),
                'other_details'   => $row[16] ?? '-',
                'remarks'         => $row[17] ?? '-',
                'father_name'     => $row[18] ?? '-',
                'exam_lab_fee'    => floatval($row[19] ?? 0),
                'development_fee' => floatval($row[20] ?? 0),
                'house'           => $row[21] ?? '-',
            ];

            $result = $wpdb->insert($table_name, $data);

            if ($result !== false) {
                $pdf_path = challan_generate_pdf($data);
                $wpdb->update($table_name, ['pdf_link' => $pdf_path], ['id' => $wpdb->insert_id]);
            } else {
               // error_log("Error inserting row $index: " . $wpdb->last_error);
            }
        }

        echo '<div class="notice notice-success"><p>Data uploaded successfully.</p></div>';
        update_challan_status();
    } else {
        echo '<div class="notice notice-error"><p>Error parsing the file: ' . SimpleXLSX::parseError() . '</p></div>';
    }
}

add_action('update_challan_status_event', 'update_challan_status');
function update_challan_status() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'challan_data';

    $wpdb->query("
        UPDATE $table_name 
        SET status = 'Overdue' 
        WHERE status = 'Unpaid' AND due_date < NOW()
    ");
}

function challan_generate_pdf($data) {
    $pdf = new PDF(); 
    $pdf->AddPage();

    // Constants
    $cardHeight = 80; 
    $y = 12; 
    $x = 12; 

    $copies = ['Bank Copy', 'School Copy', 'Parent Copy'];

    foreach ($copies as $copyLabel) {
        
        // Draw card border
        // $pdf->Rect($x, $y, 190, $cardHeight);
        $pdf->Image('https://dps.loomandlure.com/wp-content/uploads/2024/09/Screenshot_2024-09-11_183109-removebg-preview.png', $x + 5, $y , 15, 15);
        // Header Section
        $pdf->SetFont('Arial', 'BU', 14);
        $pdf->SetXY($x + 25, $y - 2);
        $pdf->Cell(150, 10, 'DISTRICT PUBLIC SCHOOL AND COLLEGE HAFIZABAD', 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x + 30, $y + 6);
        $pdf->Cell(190, 5, 'MCB (A/C# 0019 1020 1008 7063)       Punjab Bank (A/C#6510 0814 6540 0015)', 0, 1, 'L');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($x + 35, $y + 10);
        $pdf->Cell(190, 5, 'Railway Road Hafizabad                                                      Gujranwala Road, Hafizabad' , 0, 1, 'L');


        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY($x + 5, $y + 16);
        $pdf->Cell(190, 5, 'House Name:' , 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x + 27, $y + 16);
        $pdf->Cell(190, 5, $data['house'] , 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY($x , $y + 16);
        $date = $data['issue_date']; 
        $timestamp = strtotime($date);
        $pdf->Cell(190, 5, 'Fee Bill - '. date("F Y", $timestamp) , 0, 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($x, $y + 16);
        $pdf->Cell(190, 5, $copyLabel , '', 1, 'R');

        // =============================================
        // =================Left Section================
        // =============================================

        // Main Section Border
        $pdf->SetXY($x, $y + 23);
        $pdf->Cell(190, 53, '', 1); // Full border for the section

        // Registration and Student Details
        $pdf->SetXY($x + 1, $y + 24);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(27, 5, 'Registration#', 1, 0);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(33, 5, 'Issue Date', 1, 0);
        $pdf->Cell(33, 5, 'Due Date', 1, 1);

        $pdf->SetXY($x + 1, $y + 29);
        
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(27, 5, $data['enrollment_no'], 1, 0);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(33, 5, $data['issue_date'], 1, 0);
        $pdf->Cell(33, 5, $data['due_date'], 1, 1);

        // Student Name and Details
        $pdf->SetXY($x + 1, $y + 34);
        $pdf->Cell(27, 5, 'Student', 1, 0);
        $pdf->Cell(66, 5, $data['name'], 1, 1);

        $pdf->SetXY($x + 1, $y + 39);
        $pdf->Cell(27, 5, 'Father Name', 1, 0);
        $pdf->Cell(66, 5, $data['father_name'], 1, 1);

        // Class/Section, Roll No, and Code
        $pdf->SetXY($x + 1, $y + 44);
        $pdf->Cell(27, 5, 'Class/Section', 1, 0);
        $pdf->Cell(33, 5, $data['class'] . ' - ' . $data['section'], 1, 0);
        $pdf->Cell(33, 5, 'Roll No: ' . $data['roll_no'], 1, 0);

        // Note Section
        $pdf->SetXY($x + 1, $y + 50);
        $pdf->SetFont('Arial', 'BU', 12);
        $pdf->Cell(40, 5, 'N O T E:', 0, 1);

        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetXY($x + 2, $y + 55);
        $pdf->MultiCell(92, 4, "Kindly deposit the fee before the due date. The Bank will charge Rs. 100 after the due date. After 15th of the month, the name of the student will be struck off. In case of re-admission, full admission fee will be charged.");


        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetXY($x + 2, $y + 70); 
        $pdf->Cell(190, 5, "Note: This bill cannot be paid after the " . $data['last_date'] . ".", 0, 1, 'L');
        
        // =============================================
        // ================Right Section================
        // =============================================
        $pdf->SetXY($x + 96, $y + 25);
        $pdf->SetFont('Arial', 'I', 8);

        $pdf->Cell(40, 4, 'Admission Fee', 1, 0, 'L');
        $pdf->Cell(30, 4, $data['admission_fee'], 1, 1, 'C');

        $pdf->SetXY($x + 96, $y + 29.5);
        $pdf->Cell(40, 4, 'Security', 1, 0, 'L');
        $pdf->Cell(30, 4, $data['security_fee'], 1, 1, 'C');

        $pdf->SetXY($x + 96, $y + 34);
        $pdf->Cell(40, 4, 'Development Fund', 1, 0, 'L');
        $pdf->Cell(30, 4, $data['development_fee'], 1, 1, 'C');

        $pdf->SetXY($x + 96, $y + 38.5);
        $pdf->Cell(40, 4, 'Exam/Lib/Sport Fund', 1, 0, 'L');
        $pdf->Cell(30, 4, $data['exam_lab_fee'], 1, 1, 'C');

        $pdf->SetXY($x + 96, $y + 43);
        $pdf->Cell(40, 4, 'Tuition Fee', 1, 0, 'L');
        $pdf->Cell(30, 4, $data['tuition_fee'], 1, 1, 'C');

        $pdf->SetXY($x + 96, $y + 47.5);
        $pdf->Cell(40, 4, 'Computer Fee', 1, 0, 'L');
        $pdf->Cell(30, 4, $data['computer_fee'], 1, 1, 'C');

        $pdf->SetXY($x + 96, $y + 52);
        $pdf->Cell(40, 4, 'Arrears', 1, 0, 'L');
        $pdf->Cell(30, 4, $data['arrears'], 1, 1, 'C');

        $pdf->SetXY($x + 96, $y + 56.5);
        $pdf->Cell(40, 4, 'Fine', 1, 0, 'L');
        $pdf->Cell(30, 4, $data['fines'], 1, 1, 'C');

        $pdf->SetXY($x + 96, $y + 61);
        $pdf->Cell(40, 4, 'Others', 1, 0, 'L');
        $pdf->Cell(30, 4, $data['others'], 1, 1, 'C');

        $pdf->SetXY($x + 96, $y + 65.5);
        $pdf->SetFont('Arial', 'BI', 8);
        $pdf->Cell(40, 4, 'Due Date', 1, 0, 'L');
        $pdf->Cell(30, 4, ($data['admission_fee'] + $data['fines'] +  $data['security_fee'] + $data['development_fee'] + $data['computer_fee'] + $data['arrears'] + $data['others'] + $data['tuition_fee'] + $data['exam_lab_fee']), 1, 1, 'C');

        $pdf->SetXY($x + 96, $y + 70);
        $pdf->Cell(40, 4, 'After Due Date', 1, 0, 'L');
        $pdf->Cell(30, 4, ($data['admission_fee'] + $data['fines'] + $data['security_fee'] + $data['development_fee'] + $data['computer_fee'] + $data['arrears'] + $data['others'] + $data['tuition_fee'] + $data['exam_lab_fee'] + + $data['due_date_fine']), 1, 1, 'C');




        // Adjust Y for next card
        $y += $cardHeight + 10;

        // Add a new page after every 3 cards
        // if ($copyLabel === 'Parent Copy') {
        //     $pdf->AddPage();
        //     $y = 10; // Reset Y position
        // }
    }

    // Save the PDF
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . '/challans/' . $data['enrollment_no'] . '.pdf';

    if (!file_exists($upload_dir['basedir'] . '/challans')) {
        mkdir($upload_dir['basedir'] . '/challans', 0777, true);
    }

    $pdf->Output('F', $file_path);

    return $upload_dir['baseurl'] . '/challans/' . $data['enrollment_no'] . '.pdf';
}

function challan_display_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'challan_data';

    // Pagination settings
    $items_per_page = 20;
    $current_page = isset($_POST['paged']) ? max(1, intval($_POST['paged'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;

    // Handle search query
    $search_field = isset($_POST['search_field']) ? sanitize_text_field($_POST['search_field']) : '';
    $search_query = isset($_POST['search_query']) ? sanitize_text_field($_POST['search_query']) : '';
    $search_sql = '';

    if ($search_field && $search_query) {
        $search_sql = $wpdb->prepare("WHERE $search_field LIKE %s",  $search_query );
        // error_log($search_sql);
    }

    // Fetch data with pagination
    $results = $wpdb->get_results("SELECT * FROM $table_name $search_sql LIMIT $items_per_page OFFSET $offset");
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $search_sql");
    $total_pages = ceil($total_items / $items_per_page);

    echo '<h2>Challan Data</h2>';

    // Search Form
    echo '<form method="POST" style="margin-bottom: 20px; display: flex; gap: 10px;">';
    echo '<select name="search_field" style="padding: 8px; width: 180px; border: 1px solid #ddd; border-radius: 4px;">
            <option value="roll_no" ' . selected($search_field, 'roll_no', false) . '>Roll No</option>
            <option value="enrollment_no" ' . selected($search_field, 'enrollment_no', false) . '>Registration #</option>
            <option value="name" ' . selected($search_field, 'name', false) . '>Student Name</option>
            <option value="father_name" ' . selected($search_field, 'father_name', false) . '>Father Name</option>
            <option value="status" ' . selected($search_field, 'status', false) . '>Status</option>
          </select>';
    echo '<input type="text" name="search_query" value="' . esc_attr($search_query) . '" placeholder="Enter search term" style="padding: 8px; width: 250px; border: 1px solid #ddd; border-radius: 4px;">';
    echo '<input type="submit" value="Search" class="button button-primary">';
    echo '</form>';

    // Bulk Delete Form
    echo '<form method="POST" id="bulkDeleteForm">';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>';
    echo '<tr>
            <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
            <th style="width: 50px;">ID</th>
            <th style="width: 80px;">Roll No</th>
            <th>Registration #</th>
            <th>Student Name</th>
            <th>Father Name</th>
            <th>Issue Date</th>
            <th>Due Date</th>
            <th>Last Date</th>
            <th>PDF</th>
            <th>Status</th>
            <th>Action</th>
          </tr>';
    echo '</thead>';
    echo '<tbody>';

    if (!empty($results)) {
        foreach ($results as $row) {
            // Status badge styling
            $status_class = '';
            if ($row->status == 'Unpaid') {
                $status_class = 'style="background-color: gray; color: white; padding: 4px 6px; border-radius:4px;"';
            } elseif ($row->status == 'Paid') {
                $status_class = 'style="background-color: green; color: white; padding: 4px 6px; border-radius:4px;"';
            } elseif ($row->status == 'Overdue') {
                $status_class = 'style="background-color: red; color: white; padding: 4px 6px; border-radius:4px;"';
            }

            echo '<tr>';
            echo '<td style="text-align: center;"><input type="checkbox" name="delete_ids[]" value="' . esc_attr($row->id) . '"></td>';
            echo '<td>' . esc_html($row->id) . '</td>';
            echo '<td>' . esc_html($row->roll_no) . '</td>';
            echo '<td>' . esc_html($row->enrollment_no) . '</td>';
            echo '<td>' . esc_html($row->name) . '</td>';
            echo '<td>' . esc_html($row->father_name) . '</td>';
            echo '<td>' . esc_html($row->issue_date) . '</td>';
            echo '<td>' . esc_html($row->due_date) . '</td>';
            echo '<td>' . esc_html($row->last_date) . '</td>'; 
            echo '<td><a href="' . esc_url($row->pdf_link) . '" target="_blank">Download PDF</a></td>';
            echo '<td><span ' . $status_class . '>' . esc_html($row->status) . '</span></td>';
            echo '<td>';
            echo '<button class="edit-status-btn" style="background-color: blue; color: white; padding: 9px 8px; font-size: 12px; border: none; border-radius: 5px; cursor: pointer; display: inline;" data-id="' . esc_attr($row->id) . '" data-status="' . esc_attr($row->status) . '">Edit</button>';
            echo '<form method="POST" style="display:inline;">';
            echo '    <input type="hidden" name="delete_id" value="' . esc_attr($row->id) . '" />';
            echo '    <input type="submit" value="Delete" class="button button-delete" 
                        style="background-color: red; color: white; padding: 4px 8px; font-size: 12px; border: none; cursor: pointer;" 
                        onclick="return confirm(\'Are you sure you want to delete this record?\');" />';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="9">No data found.</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '<button type="submit" name="bulk_delete" class="button delete-bulk">Delete Selected</button>';
    echo '</form>';

    // Pagination Controls
    if ($total_pages > 1) {
        echo '<div class="pagination" style="margin-top: 20px; text-align: center;">';

        // First and Previous Buttons
        if ($current_page > 1) {
            echo '<form method="POST" style="display:inline;">
                    <input type="hidden" name="paged" value="1">
                    <input type="submit" class="page-btn" value="<< First">
                </form>';
            echo '<form method="POST" style="display:inline;">
                    <input type="hidden" name="paged" value="' . ($current_page - 1) . '">
                    <input type="submit" class="page-btn" value="< Prev">
                </form>';
        }

        // Page Number Buttons (Max 5 pages at a time)
        $start_page = max(1, $current_page - 2);
        $end_page = min($total_pages, $current_page + 2);

        if ($start_page > 1) {
            echo '<span class="page-dots">...</span>';
        }

        for ($i = $start_page; $i <= $end_page; $i++) {
            echo '<form method="POST" style="display:inline;">
                    <input type="hidden" name="paged" value="' . $i . '">
                    <input type="submit" class="page-btn ' . ($i == $current_page ? 'active' : '') . '" value="' . $i . '">
                </form>';
        }

        if ($end_page < $total_pages) {
            echo '<span class="page-dots">...</span>';
        }

        // Next and Last Buttons
        if ($current_page < $total_pages) {
            echo '<form method="POST" style="display:inline;">
                    <input type="hidden" name="paged" value="' . ($current_page + 1) . '">
                    <input type="submit" class="page-btn" value="Next >">
                </form>';
            echo '<form method="POST" style="display:inline;">
                    <input type="hidden" name="paged" value="' . $total_pages . '">
                    <input type="submit" class="page-btn" value="Last >>">
                </form>';
        }

        echo '</div>';
    }



    // Status Update Modal
    echo '<div id="statusModal" class="modal-overlay">
            <div class="modal-content">
                <button type="button" class="close-modal">&times;</button>
                <h2>Update Status</h2>
                <form id="updateStatusForm">
                    <input type="hidden" id="challan_id" name="challan_id">
                    <label for="status">Select Status:</label>
                    <select id="status" name="status">
                        <option value="Unpaid">Unpaid</option>
                        <option value="Paid">Paid</option>
                        <option value="Overdue">Overdue</option>
                    </select>
                    <button type="submit" class="button button-primary">Update</button>
                </form>
            </div>
          </div>';

    // Bulk Delete
    if (isset($_POST['bulk_delete']) && !empty($_POST['delete_ids'])) {
        $delete_ids = array_map('intval', $_POST['delete_ids']);
        $wpdb->query("DELETE FROM $table_name WHERE id IN (" . implode(',', $delete_ids) . ")");
        echo '<div class="notice notice-success"><p>Selected records deleted successfully.</p></div>';
    }
    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $wpdb->delete($table_name, array('id' => $delete_id));
        echo '<div class="notice notice-success"><p>Record deleted successfully.</p></div>';
    }
}

function challan_shortcodes_page() {
    ?>
    <div class="wrap">
        <h1>Challan Shortcodes</h1>
        <p>Here you can manage and use the shortcodes for your system.</p>
        
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th scope="col" class="manage-column">Shortcode Name</th>
                    <th scope="col" class="manage-column">Shortcode</th>
                    <th scope="col" class="manage-column">Copy</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th><label for="signup_shortcode">Signup Shortcode</label></th>
                    <td><input type="text" value="[challan_signup_form]" id="signup_shortcode" readonly></td>
                    <td><button onclick="copyToClipboard('signup_shortcode')" class="copy">Copy</button></td>
                </tr>
                <tr>
                    <th><label for="login_shortcode">Login Shortcode</label></th>
                    <td><input type="text" value="[challan_login_form]" id="login_shortcode" readonly></td>
                    <td><button onclick="copyToClipboard('login_shortcode')" class="copy">Copy</button></td>
                </tr>
                <tr>
                    <th><label for="search_shortcode">Search Form Shortcode</label></th>
                    <td><input type="text" value="[challan_form]" id="search_shortcode" readonly></td>
                    <td><button onclick="copyToClipboard('search_shortcode')" class="copy">Copy</button></td>
                </tr>
                <tr>
                    <th><label for="result_shortcode">Result Shortcode</label></th>
                    <td><input type="text" value="[challan_result]" id="result_shortcode" readonly></td>
                    <td><button onclick="copyToClipboard('result_shortcode')" class="copy">Copy</button></td>
                </tr>
            </tbody>
        </table>

        <script>
            function copyToClipboard(id) {
                var copyText = document.getElementById(id);
                copyText.select();
                copyText.setSelectionRange(0, 99999); 
                document.execCommand("copy");
                alert("Copied: " + copyText.value);
            }
        </script>

        
        <p>Copy and paste the shortcodes above into your pages or posts as needed.</p>
    </div>
    <?php
}

add_action('admin_footer', function () {
    ?>
    <script>
       document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("statusModal");
        const closeModalBtns = document.querySelectorAll(".close-modal");
        const updateForm = document.getElementById("updateStatusForm");
        const challanIdInput = document.getElementById("challan_id");
        const statusSelect = document.getElementById("status");

        // Define the correct AJAX URL
        const ajaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>";

        // Open Modal on Edit Button Click
        document.querySelectorAll(".edit-status-btn").forEach(btn => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                const challanId = this.getAttribute("data-id");
                const currentStatus = this.getAttribute("data-status");

                challanIdInput.value = challanId;
                statusSelect.value = currentStatus;
                modal.classList.add("show");
            });
        });

        // Close Modal on Click
        closeModalBtns.forEach(btn => {
            btn.addEventListener("click", function () {
                modal.classList.remove("show");
            });
        });

        // AJAX Status Update
        updateForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(updateForm);
        formData.append("action", "update_challan_status");

        fetch(ajaxUrl, {
            method: "POST",
            body: new URLSearchParams(formData),
        })
        .then(response => response.json())
        .then(data => {
            console.log("AJAX Response:", data);
            if (data.success) {
                alert("Status Updated Successfully!");
                modal.classList.remove("show"); // Hide modal after update
                setTimeout(() => location.reload(), 500); // Reload after short delay
            } else {
                alert("Error Updating Status: " + data.message);
            }
        })
        .catch(error => console.error("AJAX Fetch Error:", error));
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("select-all").addEventListener("click", function () {
                document.querySelectorAll('input[name="delete_ids[]"]').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        });
    </script>
    <?php
});

add_action('wp_ajax_update_challan_status', 'update_challan_status_ajax');
function update_challan_status_ajax() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'challan_data';

    // error_log('AJAX Function Reached ✅');

    if (!isset($_POST['challan_id']) || !isset($_POST['status'])) {
        error_log('AJAX ERROR: Missing Parameters ❌');
        wp_send_json_error(['message' => 'Invalid request']);
        return;
    }

    $challan_id = intval($_POST['challan_id']);
    $new_status = sanitize_text_field($_POST['status']);

    // error_log("AJAX DATA: Challan ID: $challan_id, New Status: $new_status");

    // Check if Challan Exists
    $challan_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE id = %d", $challan_id));
    if (!$challan_exists) {
        // error_log('AJAX ERROR: Challan Not Found ❌');
        wp_send_json_error(['message' => 'Challan not found']);
        return;
    }

    $updated = $wpdb->update($table_name, ['status' => $new_status], ['id' => $challan_id]);

    if ($updated !== false) {
        // error_log('AJAX SUCCESS: Status Updated ✅');
        wp_send_json_success(['message' => 'Status Updated Successfully']);
    } else {
        //error_log('AJAX ERROR: Database Update Failed ❌');
        wp_send_json_error(['message' => 'Error updating status']);
    }
}

?>