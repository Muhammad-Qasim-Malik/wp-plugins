<?php
function mqcz_add_admin_menu() {
    add_menu_page(
        'Chef Approvals',
        'Chef Approvals',
        'manage_options',
        'mqcz-chef-approvals',
        'mqcz_render_chef_approval_page',
        'dashicons-yes', 
        56
    );
}
add_action( 'admin_menu', 'mqcz_add_admin_menu' );

// Render page
function mqcz_render_chef_approval_page() {
    // Handle Approve/Decline actions
    if ( isset($_GET['action'], $_GET['user_id']) && current_user_can('administrator') ) {
        $user_id = intval($_GET['user_id']);
        if ( $_GET['action'] === 'approve' ) {
            wp_update_user( [ 'ID' => $user_id, 'role' => 'chef_active' ] );
        } elseif ( $_GET['action'] === 'decline' ) {
            delete_user_meta( $user_id, 'mqcz_document' );
            wp_update_user( [ 'ID' => $user_id, 'role' => 'food_lover' ] );
        }
    }

    $users = get_users( [ 'role' => 'chef_inactive' ] );

    echo '<div class="wrap"><h1>Pending Chef Approvals</h1>';

    if ( empty($users) ) {
        echo '<p>No pending chef requests.</p>';
        echo '</div>';
        return;
    }

    echo '<table class="widefat fixed striped">';
    echo '<thead><tr><th>Username</th><th>Email</th><th>Document</th><th>Actions</th></tr></thead><tbody>';

    foreach ( $users as $user ) {
        $doc_url = get_user_meta( $user->ID, 'mqcz_document', true );
        echo '<tr>';
        echo '<td>' . esc_html( $user->user_login ) . '</td>';
        echo '<td>' . esc_html( $user->user_email ) . '</td>';
        echo '<td>';
        echo $doc_url
            ? '<a href="' . esc_url( $doc_url ) . '" target="_blank">View</a>'
            : '<em>No document</em>';
        echo '</td>';
        echo '<td>
            <a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=mqcz-chef-approvals&action=approve&user_id=' . $user->ID ) ) . '">Approve</a>
            <a class="button button-secondary" href="' . esc_url( admin_url( 'admin.php?page=mqcz-chef-approvals&action=decline&user_id=' . $user->ID ) ) . '" style="margin-left:10px;">Decline</a>
        </td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';
}


