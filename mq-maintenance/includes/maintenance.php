<?php
// Send 503 for non-admins when maintenance is on (optional but recommended)
function mq_maintenance_headers() {
    $enabled = (int) get_option('maintenance_mode', 0);

    if ( $enabled
        && ! current_user_can('manage_options')
        && ! is_admin()
        && ! defined('DOING_AJAX')
        && ! defined('REST_REQUEST')
        && ! defined('DOING_CRON')
    ) {
        status_header(503);
        header('Retry-After: 3600');
    }
}
add_action('template_redirect', 'mq_maintenance_headers');

if ( ! function_exists('mq_maintenance_overlay') ) {
    function mq_maintenance_overlay() {
        $enabled = (int) get_option('maintenance_mode', 0);
        $heading    = get_option('maintenance_heading', '🛠️ Under Maintenance');
        $paragraph  = get_option('maintenance_paragraph', 'We’ll be back shortly.');
        $background = get_option('maintenance_background');
        $subscribe = (int) get_option('maintenance_subscribe', 0);
        if ( empty($background) ) {
            $background = 'https://images.pexels.com/photos/33590598/pexels-photo-33590598.jpeg';
        }

        if ( ! $enabled
            || current_user_can('manage_options')
            || is_admin()
            || defined('DOING_AJAX')
            || defined('REST_REQUEST')
            || defined('DOING_CRON')
        ) {
            return;
        }

        $bg_url = esc_url( $background );
        $title  = esc_html( $heading );
        $text   = esc_html( $paragraph );
        ?>
        <style>
            body {
                overflow: hidden;
            }
            #mq-maintenance-overlay::before {
                content: "";
                position: absolute;
                inset: 0; 
                background: rgba(0, 0, 0, 0.6); 
            }

            #mq-maintenance-overlay {
                position: fixed;  
                inset: 0;
                width: 100%;
                height: 100%;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-color: #111;
                color: #fff;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                text-align: center;
                gap: 20px;
                padding: 2rem;
                z-index: 999999;
            }
            #mq-maintenance-overlay > * {
                z-index: 1;
            }

        
        </style>
        <div id="mq-maintenance-overlay" style="background-image: url('<?php echo $bg_url; ?>');">
            <h1><?php echo $title; ?></h1>
            <p><?php echo $text; ?></p>
            <?php 
                if($subscribe){
            ?>
            <form id="subscribe-form">
                <h2>Subscribe Now</h2>
                <div class="subscribe-message"></div>
                <input type="email" name="email" id="subscribe-email" placeholder="Enter your Email">
                <button id="subscribe-form-submit" type="submit">Submit</button>
                </form>
            <?php 
                }
            ?>
        </div>
        <?php
    }
    add_action('wp_footer', 'mq_maintenance_overlay');
}
