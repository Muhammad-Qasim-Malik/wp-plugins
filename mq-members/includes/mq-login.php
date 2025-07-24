<?php
function mq_login_form() {
    if (is_user_logged_in() && !current_user_can('administrator')) {
        wp_redirect(home_url('/dashboard'));  
        exit;
    }

    ob_start();
    ?>
    <form method="post" class="mq_form">
        <input type="email" name="mq_email" placeholder="Email" required />
        <input type="password" name="mq_password" placeholder="Password" required />
        <label>
            <input type="checkbox" name="rememberme" value="forever" /> Remember Me
        </label><br><br>
        <input type="hidden" name="mq_login_nonce" value="<?php echo wp_create_nonce('mq_login_nonce_action'); ?>">
        <button type="submit" name="mq_login_submit" class="mq-button">Log In</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('mq_login_form', 'mq_login_form');

add_action('init', function () {
    if (!isset($_POST['mq_login_submit'])) return;

    if (!wp_verify_nonce($_POST['mq_login_nonce'], 'mq_login_nonce_action')) {
        wp_die('Security check failed.');
    }

    $email = sanitize_email($_POST['mq_email']);
    $password = $_POST['mq_password'];

    $user = get_user_by('email', $email);

    if (!$user) {
        wp_die('No user found with that email address.');
    }

    $remember = isset($_POST['rememberme']) ? true : false;

    $creds = [
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => $remember,
    ];

    $user_signon = wp_signon($creds, false);

    if (is_wp_error($user_signon)) {
        wp_die('Login failed: ' . $user_signon->get_error_message());
    }

    wp_redirect(home_url('/dashboard'));
    exit;
});
