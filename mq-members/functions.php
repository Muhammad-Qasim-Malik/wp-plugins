<?php
function mq_add_delete_permission_to_roles() {
    $roles = ['free_member', 'paid_member'];
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            $role->add_cap('delete_posts');
        }
    }
}
add_action('admin_init', 'mq_add_delete_permission_to_roles');


function set_author_role_meta( $post_id ) {
    if ( get_post_type( $post_id ) != 'muay-thai' ) {
        return;
    }

    $post = get_post( $post_id );
    $author_id = $post->post_author;

    $author = get_user_by( 'id', $author_id );
    if ( $author ) {
        $role = $author->roles[0]; 
        update_post_meta( $post_id, 'author_role', $role );
    }
}
add_action( 'save_post_muay-thai', 'set_author_role_meta' );


function custom_elementor_query_order_paid_first( $query ) {
    if ( ! is_admin() && is_tax( 'muay-thai-location' ) ) {
        $query->set( 'meta_key', 'author_role' );  
        $query->set( 'orderby', 'meta_value' );    
        $query->set( 'order', 'DSC' );             
    }
}
add_action( 'elementor/query/paid_member_first', 'custom_elementor_query_order_paid_first' );
