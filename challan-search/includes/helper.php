<?php

add_action('template_redirect', function () {
    if (is_user_logged_in() && is_target_page()) {
        wp_redirect(home_url());
        exit;
    }
});

function is_target_page() {
    if (is_page(['login', 'sign-up'])) {
        return true;
    }
    return false;
}

if (!function_exists('lms_login_handler')) {
    function lms_login_handler() {
        $feedback = '';

        if (isset($_POST['custom_login_nonce'])) {
            $username = sanitize_text_field($_POST['username']);
            $password = $_POST['password'];

            $creds = [
                'user_login'    => $username,
                'user_password' => $password,
                'remember'      => true,
            ];

            $user = wp_signon($creds, false);

            if (is_wp_error($user)) {
                $feedback = '<div class="alert alert-danger">Login failed. ' . $user->get_error_message() . '</div>';
            } else {
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID);
                wp_redirect(home_url('/challan-search'));
                exit;
            }
        }
        echo $feedback;
    }
    add_action('init', 'lms_login_handler');
}

if (!function_exists('lms_signup_handler')) {
    function lms_signup_handler() {
        $feedback = '';

        if (isset($_POST['custom_signup_nonce'])) {
            $username = sanitize_user($_POST['username']);
            $email = sanitize_email($_POST['email']);
            $password = $_POST['password'];

            if (empty($username) || empty($email) || empty($password)) {
                $feedback = '<div class="alert alert-danger">All fields are required.</div>';
            } elseif (username_exists($username) || email_exists($email)) {
                $feedback = '<div class="alert alert-danger">Username or Email already exists.</div>';
            } else {
                $user_role = 'subscriber';

                $user_id = wp_create_user($username, $password, $email);

                $user = new WP_User($user_id);
                $user->set_role($user_role);

                $creds = [
                    'user_login'    => $username,
                    'user_password' => $password,
                    'remember'      => true,
                ];

                $user = wp_signon($creds, false);
                if (is_wp_error($user)) {
                    $feedback = '<div class="alert alert-danger">Login failed. ' . $user->get_error_message() . '</div>';
                } else {
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID);
                    wp_redirect(home_url('/challan-search'));
                    exit;
                }
            }
        }
        echo $feedback;
    }
    add_action('init', 'lms_signup_handler');
}


function hide_wp_admin_bar_for_subscribers() {
    if (current_user_can('subscriber')) {
        echo '<style>#wpadminbar { display: none; }</style>';
    }
}
add_action('wp_head', 'hide_wp_admin_bar_for_subscribers');

?>
