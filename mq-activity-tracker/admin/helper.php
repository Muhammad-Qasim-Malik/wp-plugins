<?php

/* ------------------------------------------------------------
 * Helpers
 * ---------------------------------------------------------- */
function mqat_now_mysql() {
    return current_time('mysql'); // site TZ
}

function mqat_ip($fallback_to_ipv6 = true) {
    $candidates = [];

    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $candidates[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $xff) {
            $candidates[] = trim($xff);
        }
    }
    if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $candidates[] = $_SERVER['HTTP_X_REAL_IP'];
    }

    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $candidates[] = $_SERVER['REMOTE_ADDR'];
    }

    foreach ($candidates as $ip) {
        $ip = trim($ip);

        // Map IPv6 loopback to IPv4 loopback
        if ($ip === '::1') return '127.0.0.1';

        // Map IPv4-mapped IPv6 (e.g. ::ffff:192.0.2.1) to plain IPv4
        if (stripos($ip, '::ffff:') === 0) {
            $mapped = substr($ip, 7);
            if (filter_var($mapped, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $mapped;
            }
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $ip;
        }
    }

    if ($fallback_to_ipv6) {
        foreach ($candidates as $ip) {
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                return $ip;
            }
        }
    }

    return '';
}


function mqat_log($action, $object_type = '', $object_id = 0, $message = '', $meta = null) {
    global $wpdb;
    $data = [
        'logged_at'   => mqat_now_mysql(),
        'user_id'     => get_current_user_id(),
        'ip'          => mqat_ip(),
        'action'      => substr(sanitize_key($action), 0, 50),
        'object_type' => substr(sanitize_key($object_type), 0, 30),
        'object_id'   => (int)$object_id,
        'message'     => $message ? wp_kses_post($message) : null,
        'meta'        => $meta !== null ? wp_json_encode($meta) : null,
    ];
    $formats = ['%s','%d','%s','%s','%s','%d','%s','%s'];
    $wpdb->insert(MQAL_TABLE, $data, $formats);
}
