
<?php

define('JAZZCASH_MERCHANT_ID', 'MC149496');  
define('JAZZCASH_PASSWORD', 'bd2hsey00x');   
define('JAZZCASH_INTEGRITY_SALT', 'z2z4sx832x'); 
define('JAZZCASH_GATEWAY_URL', 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform'); // Sandbox URL

function jazzcash_generate_payment_form($amount, $order_id) {
    
    date_default_timezone_set('Asia/Karachi'); 
    $current_datetime = date('YmdHis');
    $expire_datetime = date('YmdHis', strtotime('+1 hour'));


    $final_amount = intval($amount * 100); 
    $tt_TxnRefNo = "TR" . date('YmdHis') . mt_rand(100, 999);

    $data = [
        'pp_Version' => '2.0',
        'pp_TxnType' => 'MWALLET',
        'pp_Language' => 'EN',
        'pp_MerchantID' => JAZZCASH_MERCHANT_ID,
        'pp_Password' => JAZZCASH_PASSWORD,
        'pp_TxnRefNo' => $tt_TxnRefNo,
        'pp_Amount' => $final_amount,  
        'pp_TxnCurrency' => 'PKR',
        'pp_TxnDateTime' => $current_datetime,
        'pp_TxnExpiryDateTime' => $expire_datetime,
        'pp_BillReference' => $order_id,
        'pp_Description' => 'Challan Payment for Order #' . $order_id,
        'pp_ReturnURL' => home_url('/wp-content/plugins/Challan Search/includes/jazzcash_callback.php'),
        'pp_CustomerID' => "Customer_" . $order_id, 
        'pp_CustomerEmail' => "customer@example.com", 
        'pp_CustomerMobile' => "03012345678",
    ];

    $data['pp_SecureHash'] = jazzcash_generate_secure_hash($data);

    $form_html = '<form action="'. JAZZCASH_GATEWAY_URL .'" method="POST" target="_blank">';
    
    // Append all fields as hidden inputs
    foreach ($data as $key => $value) {
        $form_html .= '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
    }

    $form_html .= '<button type="submit" class="challan-btn pay-now">Pay Now</button>';
    $form_html .= '</form>';

    return $form_html;
}

function jazzcash_generate_secure_hash($data) {
    if (!is_array($data)) {
        return false;
    }

    unset($data['pp_SecureHash']);

    ksort($data);

    $hash_string = JAZZCASH_INTEGRITY_SALT;
    foreach ($data as $key => $value) {
        if (!empty($value)) { 
            $hash_string .= '&' . $value;
        }
    }

    return strtoupper(hash_hmac('sha256', $hash_string, JAZZCASH_INTEGRITY_SALT));
}


function challan_result_shortcode() {
    if (!isset($_POST['challan_id'])) {
        return '<div class="show-error">
                    <p>Please use the form to search for a challan.</p>
                </div>';
    }

    $challan_id = sanitize_text_field($_POST['challan_id']);
    global $wpdb;
    $table_name = $wpdb->prefix . 'challan_data';

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE enrollment_no = %s ORDER BY issue_date DESC LIMIT 1",
        $challan_id
    ));

    if ($results) {
        ob_start(); ?>

        <style>
            .challan-card-outer {
                width: 100%;
                max-width: 900px;
                background: #ffffff;
                border-radius: 10px;
                box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
                padding: 20px;
                margin: 20px auto;
                font-family: Arial, sans-serif;
            }

            .challan-title {
                font-size: 24px;
                font-weight: bold;
                color: #0056b3;
                text-align: center;
                margin-bottom: 15px;
            }

            .challan-card {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                background: #f9f9f9;
                padding: 25px;
                border-radius: 8px;
                box-shadow: inset 0px 1px 5px rgba(0, 0, 0, 0.05);
            }

            .challan-details {
                flex: 1;
                text-align: left;
                font-size: 18px;
                font-weight: 500;
                line-height: 1.6;
                color: #333;
            }

            .challan-label {
                font-weight: bold;
                color: #222;
            }

            .button-container {
                display: flex;
                flex-direction: column;
                gap: 12px;
                text-align: right;
            }

            .challan-btn {
                background-color: #007bff;
                color: white;
                border: none;
                padding: 12px 18px;
                cursor: pointer;
                font-size: 16px;
                border-radius: 6px;
                width: 170px;
                font-weight: bold;
                transition: 0.3s;
            }

            .challan-btn.pay-now {
                background-color: #28a745;
            }

            .challan-btn:hover {
                opacity: 0.85;
                transform: scale(1.05);
            }

            .show-error {
                text-align: center;
                padding: 15px;
                background: #ffe6e6;
                border-radius: 5px;
                color: red;
                font-size: 18px;
                font-weight: bold;
            }
        </style>

        <?php
        foreach ($results as $result) {
            $amount_in_due_date = (strtolower($result->status) === 'paid') ? '<span class="status-paid">Paid</span>' :
                ($result->admission_fee + $result->fines + $result->security_fee + 
                $result->development_fee + $result->computer_fee + $result->arrears + 
                $result->others + $result->tuition_fee + $result->exam_lab_fee) . " PKR";

            // Generate JazzCash Payment Form (POST Request)
            $payment_form = jazzcash_generate_payment_form($amount_in_due_date, $result->id);
            ?>

            <div class='challan-card-outer'>
                <h3 class='challan-title'>
                    <?php echo esc_html($result->name) . ' - ' . date("F", strtotime($result->issue_date)) . ' - ' . esc_html($result->enrollment_no) ?>
                </h3>

                <div class="challan-card">
                    <div class="challan-details">
                        <p><span class="challan-label">Registration No:</span> <?php echo esc_html($result->enrollment_no); ?></p>
                        <p><span class="challan-label">Total Amount:</span> <?php echo $amount_in_due_date; ?></p>
                    </div>

                    <div class="button-container">
                        <form method='POST' action='<?php echo esc_url($result->pdf_link); ?>' target='_blank'>
                            <button class='challan-btn' type='submit'>Download PDF</button>
                        </form>

                        <?php if (strtolower($result->status) !== 'paid') { ?>
                            <?php echo $payment_form; ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }

    return ob_get_clean();
}
add_shortcode('challan_result', 'challan_result_shortcode');
?>
