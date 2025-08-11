<?php

function challan_form_shortcode() {

    if(isset($_POST['challan_id'])){
        $challan_value = $_POST['challan_id'];
    } else {
        $challan_value = "";
    }

    $output = "
    <form method='POST' action=''>
        <div class='form-group'>
            <input id='challan_input' type='number' name='challan_id' class='input-field my-custom-input' 
                placeholder='Search with your registration number (e.g., 1122) یہ رجسٹریشن نمبر آپکے فیس واؤچر پر نمایاں موجود ہے۔' 
                value='" . esc_attr($challan_value) . "' 
                min='1000' max='9999' required style='text-align: left;' />
        </div>

        <div class='form-group'>
            <input type='submit' value='Search Now' class='submit-btn' />
        </div>
    </form>";


    return $output;
}
add_shortcode('challan_form', 'challan_form_shortcode');
?>
