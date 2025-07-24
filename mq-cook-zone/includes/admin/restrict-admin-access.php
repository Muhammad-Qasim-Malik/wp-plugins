<?php

function mqcz_assign_role_permissions() {
    $role = get_role( 'chef_active' );

    if ( ! $role ) {
        return;
    }

    $role->add_cap( 'edit_special_occasions' );
    $role->add_cap( 'publish_special_occasions' );
    $role->add_cap( 'delete_special_occasions' );
    $role->remove_cap( 'edit_others_special_occasions' ); 
    $role->remove_cap( 'delete_others_special_occasions' );
    $role->add_cap( 'delete_published_special_occasions' );
    $role->add_cap( 'read_private_special_occasions' );
}
add_action( 'admin_init', 'mqcz_assign_role_permissions' );



function mqcz_restrict_chef_to_own_posts() {
    $user = wp_get_current_user();

    if ( in_array( 'chef_active', $user->roles ) ) {
        global $pagenow;
        // error_log("Hello main aa gaya");
        ?>
        <style>
            #adminmenumain { display: none !important}
            #wpcontent { margin-left: 0px !important}
        </style>
        <?php
        if ( is_admin() ) {
            // error_log("main yahan hoon");
            if ( isset($_GET['post_type']) && $_GET['post_type'] == 'special_occasions' ) {
                // error_log("main Eligible hoon");
                ?>
                
                <?php
                return; 
            }
            if ( isset($_GET['post_type']) && $_GET['post_type'] != 'special_occasions' ) {
                // error_log("Mujhe pehle redirect ker rahe hain");
                wp_redirect( site_url( '/dashboard' ) ); 
                exit;
            }

            if ( $pagenow == 'index.php' ) {
                return; 
            }
        }

        if ( 'post.php' == $pagenow && isset($_GET['post']) ) {
            $post_id = intval($_GET['post']);
            $post_author_id = get_post_field('post_author', $post_id);

            // Check if the user is the author of the post
            if ($post_author_id != $user->ID) {
                wp_redirect( site_url( '/dashboard' ) ); 
                exit;
            }
        }
    }
}
add_action('admin_init', 'mqcz_restrict_chef_to_own_posts');


