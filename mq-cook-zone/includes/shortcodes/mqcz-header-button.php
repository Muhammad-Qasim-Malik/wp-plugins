<?php
function mqcz_user_button_shortcode() {
    ob_start();

    if ( is_user_logged_in() ) {
        $dashboard_url = site_url( '/dashboard' ); 
        echo '<a href="' . esc_url( $dashboard_url ) . '" class="mqcz-btn">Go to Dashboard</a>';
    } else {
        $login_url = site_url( '/login' ); 
        echo '<a href="' . esc_url( $login_url ) . '" class="mqcz-btn">Login / Sign Up</a>';
    }

    return ob_get_clean();
}
add_shortcode( 'mqcz_user_button', 'mqcz_user_button_shortcode' );
