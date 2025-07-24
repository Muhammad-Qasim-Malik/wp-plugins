<?php 

function mq_signup_form() {
    if (is_user_logged_in() && !current_user_can('administrator')) {
        wp_redirect(home_url('/dashboard'));  
        exit;
    }

    $membership_plan = isset($_GET['membership_id']) ? intval($_GET['membership_id']) : 0;
    $is_paid = $membership_plan === 1;
    $price = 9.99;

    ob_start();
    ?>
    <form method="post" class="mq_form">
        <input type="text" name="mq_username" placeholder="Username" required />
        <input type="email" name="mq_email" placeholder="Email" required />
        <input type="password" name="mq_password" placeholder="Password" required />
        <input type="hidden" name="mq_membership_plan" value="<?php echo esc_attr($membership_plan); ?>">
        <input type="hidden" name="mq_signup_nonce" value="<?php echo wp_create_nonce('mq_signup_nonce_action'); ?>">
        <?php if ($is_paid): ?>
            <p>This is a <strong>paid membership</strong>. You'll be charged <strong>$<?php echo $price; ?></strong>.</p>
        <?php else: ?>
            <p>This is a <strong>free membership</strong>.</p>
        <?php endif; ?>
        <button type="submit" name="mq_signup_submit" class="mq-button">Sign Up</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('mq_signup_form', 'mq_signup_form');

add_action('init', function () {
    if (!isset($_POST['mq_signup_submit'])) return;

    if (!wp_verify_nonce($_POST['mq_signup_nonce'], 'mq_signup_nonce_action')) {
        wp_die('Security check failed.');
    }

    $username = sanitize_user($_POST['mq_username']);
    $email = sanitize_email($_POST['mq_email']);
    $password = $_POST['mq_password'];
    $membership_plan = intval($_POST['mq_membership_plan']);

    if ($membership_plan === 1) {
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => ['name' => 'Muay-Thai Membership'],
                        'unit_amount' => 999,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => add_query_arg([
                    'mq_paid_success' => 1,
                    'mq_username' => rawurlencode($username),
                    'mq_email' => rawurlencode($email),
                    'mq_password' => base64_encode($password),
                ], home_url('/sign-up')),
                'cancel_url' => home_url('/sign-up/?payment=cancelled'),
            ]);

            wp_redirect($session->url);
            exit;

        } catch (Exception $e) {
            wp_die('Stripe Error: ' . $e->getMessage());
        }

    } else {
        $userdata = [
            'user_login' => $username,
            'user_email' => $email,
            'user_pass'  => $password,
            'role'       => 'free_member',
        ];

        $user_id = wp_insert_user($userdata);
        if (!is_wp_error($user_id)) {
            $user = wp_signon([
                'user_login'    => $email,
                'user_password' => $password
            ], false);

            if (is_wp_error($user)) {
                wp_die('Login failed: ' . $user->get_error_message());
            }

            wp_redirect(home_url('/dashboard'));
            exit;
        } else {
            wp_die('Signup error: ' . $user_id->get_error_message());
        }
    }
});

add_action('init', function () {
    if (!isset($_GET['mq_paid_success'])) return;

    $username = sanitize_user($_GET['mq_username']);
    $email = sanitize_email($_GET['mq_email']);
    $password = base64_decode($_GET['mq_password']);

    if (username_exists($username) || email_exists($email)) return;

    $userdata = [
        'user_login' => $username,
        'user_email' => $email,
        'user_pass'  => $password,
        'role'       => 'paid_member',
    ];

    $user_id = wp_insert_user($userdata);

    if (!is_wp_error($user_id)) {
        $user = wp_signon([
            'user_login'    => $email,
            'user_password' => $password
        ], false);

        if (is_wp_error($user)) {
            wp_die('Login failed: ' . $user->get_error_message());
        }

        wp_redirect(home_url('/dashboard'));
        exit;
    } else {
        wp_die('Account creation failed: ' . $user_id->get_error_message());
    }
});

?>
