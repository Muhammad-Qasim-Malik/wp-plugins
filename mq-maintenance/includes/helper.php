<?php 

if(!function_exists('ajax_subscribe_handler')){
    function ajax_subscribe_handler(){
        if(isset($_POST['email'])){
            $email = sanitize_email($_POST['email']);
            $old_user = get_user_by('email', $email);
            if($old_user){
                wp_send_json_error("You've already subscribed.");
                return;
            } 
            $user_id = wp_create_user( $email, $email, $email );

            if ( is_wp_error( $user_id ) ) {
                wp_send_json_error("An error occurred. Please try again later.");
                return; 
            } else {
                $user = new WP_User( $user_id );
                $user->set_role( 'subscriber' ); 
                wp_send_json_success("Thanks for subscribing!");
            }

        }
    }
    add_action('wp_ajax_ajax_subscribe_handler', 'ajax_subscribe_handler');
    add_action('wp_ajax_nopriv_ajax_subscribe_handler', 'ajax_subscribe_handler');
}