<?php
add_action('wp_ajax_mqbm_get_bookings', 'mqbm_get_bookings');
add_action('wp_ajax_nopriv_mqbm_get_bookings', 'mqbm_get_bookings');

function mqbm_get_bookings() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'bookings';

    $results = $wpdb->get_results("SELECT id, service_name, booking_date, booking_time, customer_name FROM $table_name");

    $events = [];
    foreach ($results as $row) {
        $events[] = [
            'id'    => $row->id,
            'title' => $row->service_name . ' - ' . $row->customer_name,
            'start' => $row->booking_date . 'T' . $row->booking_time,
        ];
    }

    wp_send_json($events);
}