<?php
/**
 * 4. AJAX HANDLER
 */
add_action('wp_ajax_bsync_run', function(){
    $id = (int)$_POST['id'];
    $st = sanitize_text_field($_POST['sync_type']);
    $cfg = $_POST['config'] ?? [];
    
    // Get stored credentials if not in POST
    $credentials = bsync_get_credentials();
    $api_key = $cfg['api_key'] ?? $credentials['api_key'];
    $domain = rtrim($cfg['domain'] ?? $credentials['domain'], '/');
    
    if(!$api_key || !$domain) {
        wp_send_json_error(['message' => 'Missing API credentials']);
        return;
    }
    
    $wp_obj = ($st == 'users') ? get_userdata($id) : get_post($id);
    if(!$wp_obj) {
        wp_send_json_error(['message' => 'Item not found']);
        return;
    }
    
    $payload = [];
    $map = $cfg['map'] ?? [];
    $custom = $cfg['custom'] ?? [];
    
    foreach($map as $bk => $wk) {
        if(empty($wk)) continue;
        
        if($wk === '__custom__') {
            $val = $custom[$bk] ?? '';
        } else {
            $val = ($st == 'users') 
                ? get_user_meta($id, $wk, true) 
                : get_post_meta($id, $wk, true);
            
            if(!$val && isset($wp_obj->$wk)) $val = $wp_obj->$wk;
            
            if($bk == 'post_image' && is_numeric($val)) {
                $val = wp_get_attachment_url($val);
            }
        }
        
        $payload[$bk] = $val;
    }
    
    // Set endpoint and required fields
    if($st == 'users') {
        $payload['subscription_id'] = $cfg['bd_sub_id'] ?? '';
        $endpoint = "$domain/api/v2/user/create";
    } elseif($st == 'posts') {
        $payload['data_id'] = $cfg['bd_ptype'] ?? '';
        $payload['user_id'] = $cfg['bd_auth'] ?? '1';
        $payload['data_type'] = 20;
        $endpoint = "$domain/api/v2/data_posts/create";
    } else {
        $payload['data_id'] = $cfg['bd_ptype'] ?? '';
        $payload['user_id'] = $cfg['bd_auth'] ?? '1';
        $payload['data_type'] = 4;
        $endpoint = "$domain/api/v2/users_portfolio_groups/create";
    }
    
    $response = wp_remote_post($endpoint, [
        'headers' => ['X-Api-Key' => $api_key],
        'body' => $payload,
        'timeout' => 30
    ]);
    
    if(is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
        return;
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    if(isset($body['status']) && $body['status'] == 'success') {
        wp_send_json_success(['message' => 'Successfully synced']);
    } else {
        wp_send_json_error(['message' => $body['message'] ?? 'Sync failed']);
    }
});

// Test API connection
add_action('wp_ajax_bsync_test_api', function(){
    $api_key = sanitize_text_field($_POST['api_key'] ?? '');
    $domain = esc_url_raw($_POST['domain'] ?? '');
    
    if(!$api_key || !$domain) {
        wp_send_json_error();
        return;
    }
    
    $response = wp_remote_get("$domain/api/v2/user/read", [
        'headers' => ['X-Api-Key' => $api_key],
        'timeout' => 10
    ]);
    
    if(is_wp_error($response)) {
        wp_send_json_error();
    } else {
        wp_send_json_success();
    }
});
