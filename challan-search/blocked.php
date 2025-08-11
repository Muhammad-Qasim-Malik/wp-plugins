<?php 
function challan_edit_form() {

    update_challan_status();
    
    if (isset($_POST['update_challan'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'challan_data';
    
        $update_id = intval($_POST['update_id']);
        $updated_data = [];
    
        foreach ($_POST as $key => $value) {
            if ($key !== 'update_challan' && $key !== 'update_id') {
                $updated_data[$key] = in_array($key, ['admission_fee', 'security_fee', 'tuition_fee', 'computer_fee', 'advance_fee', 'arrears', 'fines', 'due_date_fine', 'others', 'exam_lab_fee', 'development_fee']) ? (float)$value : sanitize_text_field($value);
            }
        }
    
        // Update the database

        $pdf_path = challan_generate_pdf($updated_data);
        $wpdb->update($table_name, ['pdf_link' => $pdf_path], ['id' => $update_id]);

        $wpdb->update($table_name, $updated_data, ['id' => $update_id]);
    
        echo '<div class="notice notice-success"><p>Challan updated successfully.</p></div>';
        
        echo '<script>setTimeout(function() { window.location.href = "' . admin_url('admin.php?page=challan_display_page') . '"; }, 2000);</script>';
        
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'challan_data';

    // Check if an ID is provided
    $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;
    $challan_data = null;

    if ($edit_id) {
        $challan_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $edit_id), ARRAY_A);
    }

    echo '<div class="wrap" style="display: flex; justify-content: center; align-items: center; height: auto; flex-direction: column;">';

    // FORM 1: SEARCH CHALLAN ID
    if (!$edit_id) {
        echo '<div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; max-width: 400px;">';
        echo '<h2 style="text-align:center;">Enter Challan ID</h2>';
        echo '<form method="POST" style="display: flex; flex-direction: column; gap: 10px;">';
        echo '<label style="font-weight: bold;">Challan ID:</label>';
        echo '<input type="number" name="edit_id" required style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">';
        echo '<input type="submit" value="Search" class="button button-primary" style="background-color: #0073aa; color: white; padding: 10px; font-size: 16px; cursor: pointer; border-radius: 4px;">';
        echo '</form>';
        echo '</div>';
    }

    // FORM 2: EDIT CHALLAN DETAILS (ONLY IF ID IS FOUND)
    if ($edit_id && $challan_data) {
        echo '<div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; max-width: 600px;">';
        echo '<h2 style="text-align:center;">Edit Challan Details</h2>';
        echo '<form method="POST" style="display: flex; flex-direction: column; gap: 15px;">';

        // Hidden field to pass the ID
        echo '<input type="hidden" name="update_id" value="' . esc_attr($edit_id) . '">';

        // Fields for Editing
        $fields = [
            'issue_date' => 'Issue Date',
            'due_date' => 'Due Date',
            'enrollment_no' => 'Enrollment No',
            'name' => 'Student Name',
            'father_name' => 'Father Name',
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
            'exam_lab_fee' => 'Exam Lab Fee',
            'development_fee' => 'Development Fee',
            'house' => 'House'
        ];

        foreach ($fields as $name => $label) {
            $input_type = in_array($name, ['issue_date', 'due_date', 'admission_date']) ? 'date' : 
                          (in_array($name, ['admission_fee', 'security_fee', 'tuition_fee', 'computer_fee', 'advance_fee', 'arrears', 'fines', 'due_date_fine', 'others', 'exam_lab_fee', 'development_fee']) ? 'number' : 'text');

            $extra_attr = ($name === 'enrollment_no') ? "minlength='4' maxlength='4' disabled title='Must be exactly 4 digits'" : "";
            $value = esc_attr($challan_data[$name] ?? '');

            echo "<label style='font-weight: bold;'>{$label}:</label>";
            echo "<input type='{$input_type}' step='0.01' name='{$name}' value='{$value}' required {$extra_attr} style='padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;'>";
        }

        // ✅ STATUS DROPDOWN
        echo "<label style='font-weight: bold;'>Status:</label>";
        echo "<select name='status' required style='padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;'>";
        echo "<option value='Unpaid' " . selected($challan_data['status'], 'Unpaid', false) . ">Unpaid</option>";
        echo "<option value='Paid' " . selected($challan_data['status'], 'Paid', false) . ">Paid</option>";
        echo "</select>";

        echo '<input type="submit" name="update_challan" value="Update" class="button button-primary" style="background-color: #28a745; color: white; padding: 12px; font-size: 16px; cursor: pointer; border-radius: 4px;">';
        echo '</form>';
        echo '</div>';
    }

    echo '</div>';

    if ($edit_id && !$challan_data) {
        echo '<div class="notice notice-error"><p>No Challan found with this ID. Please try again.</p></div>';
        echo '<script>setTimeout(function() { window.location.href = "' . admin_url('admin.php?page=challan-edit') . '"; }, 2000);</script>';
        return;
    }
}

add_submenu_page(
    'challan_display_page',              
    'Challan Edit',             
    'Challan Edit',             
    'manage_options',                  
    'challan-edit',             
    'challan_edit_form'         
);