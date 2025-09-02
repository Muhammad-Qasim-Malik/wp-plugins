<?php
// includes/helper.php


function get_weekly_schedule() {
    return get_option('mq_weekly_schedule', []);
}

function is_day_off($day) {
    $schedule = get_weekly_schedule();
    return isset($schedule[$day]['off']) && $schedule[$day]['off'];
}

function get_day_times($day) {
    $schedule = get_weekly_schedule();
    if (is_day_off($day)) {
        return [];
    }
    return [
        'start' => $schedule[$day]['start'] ?? '09:00',
        'end' => $schedule[$day]['end'] ?? '17:00'
    ];
}

function get_slot_interval() {
    return 30; // minutes
}

function generate_available_slots($date) {
    $day = strtolower(date('l', strtotime($date)));
    $times = get_day_times($day);
    if (empty($times) || strtotime($date) < strtotime('today')) {
        return [];
    }
    $start = strtotime($times['start']);
    $end = strtotime($times['end']);
    $interval = get_slot_interval() * 60; // seconds
    $slots = [];
    $current = $start;
    while ($current <= $end) {
        $time = date('H:i', $current);
        if (!is_slot_booked($date, $time)) {
            $slots[] = $time;
        }
        $current += $interval;
    }
    return $slots;
}

function is_slot_booked($date, $time) {
    global $wpdb;
    $table = $wpdb->prefix . 'bookings';
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE booking_date = %s AND booking_time = %s", $date, $time));
    return $count > 0;
}

add_action('wp_ajax_mqbm_get_times', 'mqbm_get_times_handler');
add_action('wp_ajax_nopriv_mqbm_get_times', 'mqbm_get_times_handler');
function mqbm_get_times_handler() {
    check_ajax_referer('mqbm_user_nonce', 'nonce');
    $date = sanitize_text_field($_POST['date']);
    $times = generate_available_slots($date);
    if (empty($times)) {
        wp_send_json_error(['message' => 'No available times for this date']);
    }
    wp_send_json_success(['times' => $times]);
    error_log('Get times request for date: ' . $date . ', slots: ' . json_encode($times));
}

add_action('wp_ajax_mqbm_get_calendar', 'mqbm_get_calendar_handler');
add_action('wp_ajax_nopriv_mqbm_get_calendar', 'mqbm_get_calendar_handler');
function mqbm_get_calendar_handler() {
    check_ajax_referer('mqbm_user_nonce', 'nonce');
    $month = intval($_POST['month']);
    $year = intval($_POST['year']);
    // Default to current month and year if invalid
    if ($month < 1 || $month > 12) $month = date('n');
    if ($year < 1970 || $year > 2100) $year = date('Y');
    ob_start();
    mqbm_generate_calendar_html($month, $year);
    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
    error_log('Calendar generated for month: ' . $month . ', year: ' . $year);
}

function mqbm_generate_calendar_html($month, $year) {
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $first_day = date('w', mktime(0, 0, 0, $month, 1, $year));
    $today = strtotime(date('Y-m-d'));
    ?>
    <div class="calendar-header">
        <button id="prev-month">&lt;</button>
        <span id="current-month"><?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?></span>
        <button id="next-month">&gt;</button>
    </div>
    <table class="calendar">
        <thead>
            <tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>
        </thead>
        <tbody>
            <?php
            $day_count = 1;
            for ($row = 0; $row < 6; $row++) {
                echo '<tr>';
                for ($col = 0; $col < 7; $col++) {
                    if (($row == 0 && $col < $first_day) || $day_count > $days_in_month) {
                        echo '<td></td>';
                    } else {
                        $date_str = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day_count, 2, '0', STR_PAD_LEFT);
                        $day_of_week = strtolower(date('l', strtotime($date_str)));
                        $class = 'day';
                        if (strtotime($date_str) < $today) $class .= ' past';
                        elseif (is_day_off($day_of_week)) $class .= ' off';
                        if (date('Y-m-d') == $date_str) $class .= ' today';
                        echo '<td class="' . $class . '" data-date="' . $date_str . '">' . $day_count . '</td>';
                        $day_count++;
                    }
                }
                echo '</tr>';
                if ($day_count > $days_in_month) break;
            }
            ?>
        </tbody>
    </table>
    <button id="check-next-avail">Check Next Availability</button>
    <?php
}

add_action('wp_ajax_mqbm_book', 'mqbm_book_handler');
add_action('wp_ajax_nopriv_mqbm_book', 'mqbm_book_handler');
function mqbm_book_handler() {
    check_ajax_referer('mqbm_user_nonce', 'nonce');
    parse_str($_POST['form_data'], $data);
    $service = sanitize_text_field($data['service']);
    $first = sanitize_text_field($data['first']);
    $last = sanitize_text_field($data['last']);
    $email = sanitize_email($data['email']);
    $phone = sanitize_text_field($data['phone']);
    $message = sanitize_textarea_field($data['message'] ?? '');
    $date = sanitize_text_field($data['date']);
    $time = sanitize_text_field($data['time']);
    if (empty($first) || empty($last) || empty($email) || empty($date) || empty($time)) {
        wp_send_json_error(['message' => 'All required fields must be filled']);
    }
    if (is_slot_booked($date, $time)) {
        wp_send_json_error(['message' => 'This slot is already booked']);
    }
    global $wpdb;
    $table = $wpdb->prefix . 'bookings';
    $inserted = $wpdb->insert($table, [
        'service_name' => $service,
        'customer_name' => $first . ' ' . $last,
        'customer_email' => $email,
        'customer_phone' => $phone,
        'customer_message' => $message,
        'booking_date' => $date,
        'booking_time' => $time
    ]);
    if ($inserted) {
        wp_send_json_success();
        error_log('Booking saved: ' . $first . ' ' . $last . ' on ' . $date . ' at ' . $time);
    } else {
        wp_send_json_error(['message' => 'Failed to save booking']);
    }
}