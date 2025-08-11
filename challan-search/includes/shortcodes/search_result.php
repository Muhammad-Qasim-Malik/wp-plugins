<?php 
function challan_result_shortcode() {
    if (!isset($_POST['challan_id'])) {
        return '<div class="show-error">
                    <p>Please use the form to search for a challan.</p>
                </div>';
    }

    $challan_id = sanitize_text_field($_POST['challan_id']);
    global $wpdb;
    $table_name = $wpdb->prefix . 'challan_data';

    // Fetch the latest challan data based on enrollment_no
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

            @media (max-width: 768px) {
                .challan-card {
                    flex-direction: column;
                    text-align: center;
                    padding: 20px;
                }
                
                .button-container {
                    text-align: center;
                    margin-top: 15px;
                }
            }
        </style>

        <?php
        foreach ($results as $result) {
            // Get last_date from the database
            $last_date = $result->last_date; 

            if (strtolower($result->status) === 'paid') {
                $amount_in_due_date = '<span class="status-paid">Paid</span>';
                $amount_after_due_date = '<span class="status-paid">Paid</span>';
            } else {
                $amount_in_due_date = $result->admission_fee + $result->fines + $result->security_fee + 
                    $result->development_fee + $result->computer_fee + $result->arrears + 
                    $result->others + $result->tuition_fee + $result->exam_lab_fee;

                $amount_after_due_date = $amount_in_due_date + $result->due_date_fine;

                // Append currency symbol
                $amount_in_due_date .= " PKR";
                $amount_after_due_date .= " PKR";
            }

            // Check if current date is after the last date
            $current_date = date("Y-m-d");
            $is_after_last_date = strtotime($current_date) > strtotime($last_date);
            ?>

            <div class='challan-card-outer'>
            <?php 
               $month_year = date("F Y", strtotime($result->issue_date));
            ?>

                <h3 class="challan-title">
                    <span>Fee of the Month: <b class="dynamic-text"><?php echo $month_year; ?></b></span>
                    <span>Registration No: <b class="dynamic-text"><?php echo esc_html($result->enrollment_no); ?></b></span>
                    <span>Due Date: <b class="dynamic-text"><?php echo esc_html($result->due_date); ?></b></span>
                </h3>

                <div class="challan-card">
                    <div class="challan-details">
                        <p><span class="challan-label">Student Name:</span> <b class="dynamic-detail-text"><?php echo esc_html($result->name); ?></b></p>
                        <p><span class="challan-label">Father Name:</span> <?php echo esc_html($result->father_name); ?></p>
                        <p><span class="challan-label">Class/Section:</span> <span class="dynamic-detail-text"><?php echo esc_html($result->class) . ' - ' . esc_html($result->section); ?></span></p>
                        <p><span class="challan-label">Due Amount:</span> <?php echo $amount_in_due_date; ?></p>
                        <p class="challan-note"><span class="challan-label">Note:</span> A fine will be added after the due date.</p>
                    </div>
                    <div class="button-container">
                        <form method='POST' action='<?php echo esc_url($result->pdf_link); ?>' target='_blank'>
                            <button class='challan-btn' type='submit'>Print Preview</button>
                        </form>

                        <?php if (strtolower($result->status) !== 'paid') { ?>
                            <?php if ($is_after_last_date) { ?>
                                <!-- Show alert when last date has passed -->
                                <button class="challan-btn" onclick="alert('The last date has passed. You cannot pay now.');">Pay Now</button>
                            <?php } else { ?>
                                <form id="paymentForm" method="POST" action="https://metaviz.pro" target="_blank">
                                    <button class="pay-now challan-btn" type="submit">Pay Now</button>
                                </form>
                            <?php } ?>

                            <script>
                                document.getElementById("paymentForm").addEventListener("submit", function(event) {
                                    event.preventDefault(); // Prevent form submission
                                    alert("This feature is under construction.");
                                });
                            </script>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php
        }
    } else {
        echo '<div class="show-error">
                <p>No record found. Please try again.</p>
              </div>';
    }

    return ob_get_clean();
}
add_shortcode('challan_result', 'challan_result_shortcode');
