<?php

function mv_login_form_shortcode() {
    ob_start();
    ?>
    <form method="POST" action="" class="my-challan-form" style="max-width: 500px; margin: auto;">
        <?php wp_nonce_field('custom_login_action', 'custom_login_nonce'); ?>
        
        <div class="form-group">
            <label for="username" class="label">CNIC Number</label>
            <input type="text" name="username" id="challan_input" class="input-field" placeholder="Type your CNIC (e.g., XXXXX-XXXXXXX-X)" maxlength="15" minlength="15" required />
        </div>
        
        <div class="form-group">
            <label for="password" class="label">Password</label>
            <input type="password" name="password" id="password" class="input-field" placeholder="Enter your password" required />
        </div>
        
        <div class="form-group">
            <button type="submit" class="submit-btn">Log In</button>
        </div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('challan_login_form', 'mv_login_form_shortcode');
