<?php
function mqcz_register_form_shortcode() {
    if ( is_user_logged_in() && !current_user_can( 'administrator' ) ) {
        wp_redirect( site_url( '/dashboard' ) );
        exit;
    }

    $error = '';
    $success = '';

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['mqcz_register_nonce'] ) ) {
        if ( wp_verify_nonce( $_POST['mqcz_register_nonce'], 'mqcz_register_action' ) ) {
            $username = sanitize_user( $_POST['mqcz_username'] );
            $email    = sanitize_email( $_POST['mqcz_email'] );
            $first_name = sanitize_text_field( $_POST['mqcz_first_name'] );
            $last_name  = sanitize_text_field( $_POST['mqcz_last_name'] );
            $password = $_POST['mqcz_password'];
            $confirm_password = $_POST['mqcz_confirm_password'];
            $role     = $_POST['mqcz_role'] === 'chef' ? 'chef_inactive' : 'food_lover';
            $professional_summary = sanitize_textarea_field( $_POST['mqcz_professional_summary'] );

            // Password and Confirm Password validation
            if ( $password !== $confirm_password ) {
                $error = 'Passwords do not match.';
            }

            if ( username_exists( $username ) || email_exists( $email ) ) {
                $error = 'Username or email already exists.';
            } else {
                $user_id = wp_create_user( $username, $password, $email );

                if ( ! is_wp_error( $user_id ) ) {
                    wp_update_user( [
                        'ID' => $user_id, 
                        'role' => $role,
                        'first_name' => $first_name,
                        'last_name' => $last_name
                    ]);

                    // If chef, handle document upload
                    if ( $role === 'chef_inactive' && ! empty( $_FILES['mqcz_document']['name'] ) ) {
                        require_once ABSPATH . 'wp-admin/includes/file.php';
                        $upload = wp_handle_upload( $_FILES['mqcz_document'], ['test_form' => false] );
                        if ( ! isset( $upload['error'] ) ) {
                            update_user_meta( $user_id, 'mqcz_document', esc_url( $upload['url'] ) );
                        } else {
                            $error = 'File upload error: ' . $upload['error'];
                        }
                    }

                    // Save Professional Summary
                    update_user_meta( $user_id, 'mqcz_professional_summary', $professional_summary );

                    if ( empty( $error ) ) {
                        $success = 'Registration successful. You can now log in.';
                    }
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        } else {
            $error = 'Security check failed.';
        }
    }

    ob_start();
    ?>

    <form method="post" class="mqcz-form" enctype="multipart/form-data">
        <?php 
            if ( ! empty( $error ) ) {
                echo '<div class="mqcz-error">' . esc_html( $error ) . '</div>';
            }

            if ( ! empty( $success ) ) {
                echo '<div class="mqcz-success">' . esc_html( $success ) . '</div>';
            }
        ?>
        <p>
            <label for="mqcz_username">Username*</label><br>
            <input type="text" name="mqcz_username" id="mqcz_username" required>
        </p>
        <p>
            <label for="mqcz_email">Email*</label><br>
            <input type="email" name="mqcz_email" id="mqcz_email" required>
        </p>
        <p>
            <label for="mqcz_first_name">First Name</label><br>
            <input type="text" name="mqcz_first_name" id="mqcz_first_name">
        </p>
        <p>
            <label for="mqcz_last_name">Last Name</label><br>
            <input type="text" name="mqcz_last_name" id="mqcz_last_name">
        </p>
       <p>
            <label for="mqcz_password">Password*</label><br>
            <div class="password-field" style="position: relative;">
                <input type="password" name="mqcz_password" id="mqcz_password" required>
                <div class="eye-icon"><i class="fas fa-eye" style="cursor: pointer; position: absolute; right: 10px; top: 8px; transform: translateY(50%);"></i></div>
            </div>
        </p>
        <p>
            <label for="mqcz_confirm_password">Confirm Password*</label><br>
            <div class="password-field" style="position: relative;">
                <input type="password" name="mqcz_confirm_password" id="mqcz_confirm_password" required>
                <div class="eye-icon"><i class="fas fa-eye" style="cursor: pointer; position: absolute; right: 10px; top: 8px; transform: translateY(50%);"></i></div>
            </div>
        </p>


        <p>
            <label for="mqcz_role">Joining as:</label><br>
            <select name="mqcz_role" id="mqcz_role" required>
                <option value="">Select Role</option>
                <option value="food_lover">Food Lover</option>
                <option value="chef">Chef</option>
            </select>
        </p>
        <p id="mqcz-doc-upload" style="display: none;">
            <label for="mqcz_document">Upload Document (PDF, JPG, etc.) for Identification</label><br>
            <input type="file" name="mqcz_document" id="mqcz_document" accept=".jpg,.jpeg,.png,.pdf">
        </p>
        <p>
            <label for="mqcz_professional_summary">Professional Summary</label><br>
            <textarea name="mqcz_professional_summary" id="mqcz_professional_summary" rows="4"></textarea>
        </p>
        <p>
            <label for="mqcz_terms_conditions">
                <input type="checkbox" name="mqcz_terms_conditions" id="mqcz_terms_conditions" required>
                I accept the <a href="/terms-and-conditions" target="_blank">Terms & Conditions</a> by Foodies Network.
            </label>
        </p>
        <?php wp_nonce_field( 'mqcz_register_action', 'mqcz_register_nonce' ); ?>
        <p>
            <button type="submit" class="mqcz-btn">Register</button>
        </p>
        
        <p>
            Already have an account? 
            <a href="<?php echo esc_url( site_url( '/login' ) ); ?>">Log in here</a>.
        </p>
    </form>

    <?php
    return ob_get_clean();
}
add_shortcode( 'mqcz_register_form', 'mqcz_register_form_shortcode' );
?>
