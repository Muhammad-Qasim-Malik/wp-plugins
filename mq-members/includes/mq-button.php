<?php
function mq_user_button() {
    if (is_user_logged_in()) {
        return '<a href="' . home_url('/dashboard') . '" class="mq-button mq-button-red">Dashboard</a>';
    } else {
        $join_url = home_url('/membership-plans');  
        $login_url = home_url('/login');   
        return '
        <div class="mq-top-buttons">
            <a href="' . esc_url($join_url) . '" class="mq-button mq-button-red">Join</a>
            <a href="' . esc_url($login_url) . '" class="mq-button ">Login</a>
        </div>
        ';
    }
}
add_shortcode('mq_user_button', 'mq_user_button');
