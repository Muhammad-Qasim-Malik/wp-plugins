<?php
function mqcz_login_form_shortcode() {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();

        $roles = $user->roles;
        
        if ( in_array( 'chef_active', $roles ) || in_array( 'chef_inactive', $roles ) ) {
            wp_redirect( site_url( '/dashboard' ) );
            exit;
        } elseif ( in_array( 'administrator', $roles ) ) {
            wp_redirect( home_url() );
            exit;
        } else {
            wp_redirect( site_url( '/profilegrid-profile/?uid=' . $user->ID ) );
            exit;
        }
    }


    $error = '';

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['mqcz_login_nonce'] ) ) {
        if ( wp_verify_nonce( $_POST['mqcz_login_nonce'], 'mqcz_login_action' ) ) {
            
            $username = sanitize_user( $_POST['mqcz_username'] );
            $password = $_POST['mqcz_password'];
            $remember = isset( $_POST['mqcz_remember'] ) ? true : false;

            $creds = [
                'user_login'    => $username,
                'user_password' => $password,
                'remember'      => $remember,
            ];

            $user = wp_signon( $creds, is_ssl() );

            if ( is_wp_error( $user ) ) {
                $error = 'Invalid username or password.';
            } else {

                $roles = $user->roles;

                if ( in_array( 'administrator', $roles ) ) {
                    wp_redirect( admin_url() ); 
                } elseif ( in_array( 'chef_active', $roles ) || in_array( 'chef_inactive', $roles ) ) {
                    wp_redirect( site_url( '/dashboard' ) );
                } else {
                    wp_redirect( site_url( '/profilegrid-profile/?uid=' . $user->ID ) ); 
                }
                exit; 
            }
        } else {
            $error = 'Security check failed.';
        }
    }


    ob_start();
    ?>

    <form method="post" class="mqcz-form">
        <?php 
        if ( ! empty( $error ) ) {
            echo '<div class="mqcz-error">' . esc_html( $error ) . '</div>';
        }
        ?>
        <p>
            <label for="mqcz_username">Username or Email</label><br>
            <input type="text" name="mqcz_username" id="mqcz_username" required>
        </p>
        <p>
            <label for="mqcz_password">Password</label><br>
            <input type="password" name="mqcz_password" id="mqcz_password" required>
        </p>
        <p>
            <label>
                <input type="checkbox" name="mqcz_remember" /> Remember Me
            </label>
        </p>
        <?php wp_nonce_field( 'mqcz_login_action', 'mqcz_login_nonce' ); ?>
        <p>
            <button type="submit" class="mqcz-btn">Login</button>
        </p>
        <p>
            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Forgot password?</a> |
            <a href="<?php echo esc_url( site_url( '/signup' ) ); ?>">Sign up</a>
        </p>
    </form>

    <?php
    return ob_get_clean();
}
add_shortcode( 'mqcz_login_form', 'mqcz_login_form_shortcode' );
