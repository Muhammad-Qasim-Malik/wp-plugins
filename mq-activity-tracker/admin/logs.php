<?php
/* ------------------------------------------------------------
 * Hooks: Log common activities
 * ---------------------------------------------------------- */

add_action('wp_login', function ($user_login, $user) {
    mqat_log('login_success', 'user', $user->ID, "User '{$user_login}' logged in.");
}, 10, 2);

add_action('wp_login_failed', function ($username) {
    mqat_log('login_failed', 'user', 0, "Failed login for '{$username}'.");
});

add_action('user_register', function ($user_id) {
    $u = get_userdata($user_id);
    mqat_log('user_register', 'user', $user_id, "User registered: " . ($u ? $u->user_login : $user_id));
});

add_action('profile_update', function ($user_id, $old_user_data) {
    $u = get_userdata($user_id);
    mqat_log('user_update', 'user', $user_id, "Profile updated: " . ($u ? $u->user_login : $user_id));
}, 10, 2);

add_action('delete_user', function ($user_id) {
    mqat_log('user_delete', 'user', $user_id, "User deleted: {$user_id}");
});

add_action('transition_post_status', function ($new, $old, $post) {
    if ($post->post_type === 'revision') return;
    if ($new === $old) return;
    $title = get_the_title($post);
    mqat_log('post_status', $post->post_type, $post->ID, "Post '{$title}' status: {$old} → {$new}");
}, 10, 3);

add_action('wp_insert_comment', function ($comment_id, $comment) {
    mqat_log('comment_add', 'comment', $comment_id, "New comment on post {$comment->comment_post_ID} by {$comment->comment_author}");
}, 10, 2);

add_action('transition_comment_status', function ($new, $old, $comment) {
    if ($new === $old) return;
    mqat_log('comment_status', 'comment', $comment->comment_ID, "Comment status: {$old} → {$new}");
}, 10, 3);

add_action('add_attachment', function ($post_id) {
    $p = get_post($post_id);
    mqat_log('media_add', 'attachment', $post_id, "Media uploaded: " . ($p ? $p->post_title : $post_id));
});
add_action('delete_attachment', function ($post_id) {
    mqat_log('media_delete', 'attachment', $post_id, "Media deleted: {$post_id}");
});

add_action('activated_plugin', function ($plugin) {
    mqat_log('plugin_activated', 'plugin', 0, "Activated: {$plugin}");
});
add_action('deactivated_plugin', function ($plugin) {
    mqat_log('plugin_deactivated', 'plugin', 0, "Deactivated: {$plugin}");
});

add_action('switch_theme', function () {
    $theme = wp_get_theme();
    mqat_log('theme_switch', 'theme', 0, "Theme switched to: " . $theme->get('Name'));
});

add_action('updated_option', function ($option, $old_value, $value) {
    $watch = [
        'blogname', 'blogdescription', 'siteurl', 'home',
        'admin_email', 'timezone_string', 'permalink_structure'
    ];
    if (in_array($option, $watch, true)) {
        mqat_log('option_update', 'option', 0, "Option '{$option}' updated.");
    }
}, 10, 3);