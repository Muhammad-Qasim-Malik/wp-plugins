<?php
add_action('wp_ajax_mqwr_get_weather', 'mqwr_get_weather');
add_action('wp_ajax_nopriv_mqwr_get_weather', 'mqwr_get_weather');

function mqwr_get_weather() {
    check_ajax_referer('mqwr_nonce', 'nonce');

    $city = sanitize_text_field($_POST['city']);
    $units = in_array($_POST['units'], ['metric','imperial']) ? $_POST['units'] : 'metric';

    $api_key = get_option('mqwr_api_key');
    if (!$api_key) wp_send_json_error('API key not set');

    $url = add_query_arg([
        'q' => $city,
        'appid' => $api_key,
        'units' => $units,
    ], 'https://api.openweathermap.org/data/2.5/weather');

    $res = wp_remote_get($url);
    if (is_wp_error($res)) wp_send_json_error($res->get_error_message());

    $body = wp_remote_retrieve_body($res);
    $data = json_decode($body, true);

    if (empty($data) || !isset($data['main'])) wp_send_json_error('No data found');

    wp_send_json_success($data);
}