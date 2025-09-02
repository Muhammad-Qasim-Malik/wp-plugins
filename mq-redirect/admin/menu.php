<?php

function register_mq_redirect_menu(){
    add_menu_page(
        'MQ Redirect',
        'MQ Redirect',
        'manage_options',
        'mq-redirect',
        'mq_redirect_dashboard',
        'dashicons-arrow-right-alt',
        12
    );
}

add_action('admin_menu', 'register_mq_redirect_menu');