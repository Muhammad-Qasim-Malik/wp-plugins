// function mqcz_first_special_occasions_post_popup_on_publish($post_id) {

//     if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
//     if (get_post_type($post_id) !== 'special_occasions' || get_post_status($post_id) !== 'publish') return;
//     if (get_post_meta($post_id, '_mqcz_first_post_popup_shown', true)) return;

//     $user_id = get_post_field('post_author', $post_id);
//     $args = [
//         'post_type' => 'special_occasions',
//         'author' => $user_id,
//         'posts_per_page' => -1, 
//         'post_status' => 'publish', 
//     ];
//     $published_posts = get_posts($args);

//     $first_post = count($published_posts) === 1; 
//     if ($first_post) {
//         set_transient('mqcz_first_special_occasions_popup', true, 600);
//     }

//     update_post_meta($post_id, '_mqcz_first_post_popup_shown', true);
// }
// add_action('publish_special_occasions', 'mqcz_first_special_occasions_post_popup_on_publish');

// function mqcz_display_first_post_popup() {
//     // Check if the transient exists (this means the user just published their first post)
//     if (get_transient('mqcz_first_special_occasions_popup')) {
//         // Only display on the singular post edit page (post.php?post=ID&action=edit)
//         if (is_admin() && isset($_GET['post']) && $_GET['action'] === 'edit') {
//             $post_id = (int) $_GET['post'];
//             if (get_post_meta($post_id, '_mqcz_first_post_popup_shown', true)) {
//                 ?>
<!-- //                 <div id="mqcz-popup-overlay" style="display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9998;"></div>
//                 <div id="mqcz-first-post-popup" style="display: block; position: fixed; top: 30%; left: 50%; transform: translateX(-50%); background: #28a745; color: white; padding: 40px; border-radius: 15px; z-index: 9999; font-size: 20px; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); max-width: 500px; width: 100%; text-align: center;">
//                     <strong style="font-size: 24px;">🎉 Congratulations! 🎉</strong><br><br>
//                     <p style="font-size: 18px;">You've successfully created your first special occasion! Keep up the great work!</p>
//                     <button id="mqcz-popup-close" style="margin-top: 20px; background: white; color: #28a745; border: none; padding: 10px 20px; cursor: pointer; border-radius: 10px; font-size: 16px; transition: background-color 0.3s;">
//                         Close
//                     </button>
//                 </div> -->

//                 <script type="text/javascript">
//                     document.addEventListener('DOMContentLoaded', function() {
//                         var popup = document.getElementById('mqcz-first-post-popup');
//                         var overlay = document.getElementById('mqcz-popup-overlay');
//                         var closeButton = document.getElementById('mqcz-popup-close');

//                         // Close the popup when the close button or overlay is clicked
//                         closeButton.addEventListener('click', function() {
//                             popup.style.display = 'none';
//                             overlay.style.display = 'none';
//                         });

//                         overlay.addEventListener('click', function() {
//                             popup.style.display = 'none';
//                             overlay.style.display = 'none';
//                         });
//                     });
//                 </script>

//                 <style type="text/css">
//                     /* Enhanced Popup Styles */
//                     #mqcz-first-post-popup {
//                         position: fixed;
//                         top: 30%;
//                         left: 50%;
//                         transform: translateX(-50%);
//                         background: #28a745;
//                         color: white;
//                         padding: 40px;
//                         border-radius: 15px;
//                         z-index: 9999;
//                         font-size: 20px;
//                         box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
//                         max-width: 500px;
//                         width: 100%;
//                         text-align: center;
//                     }

//                     #mqcz-first-post-popup button {
//                         background: white;
//                         color: #28a745;
//                         border: none;
//                         padding: 10px 20px;
//                         cursor: pointer;
//                         border-radius: 10px;
//                         font-size: 16px;
//                         transition: background-color 0.3s;
//                     }

//                     #mqcz-first-post-popup button:hover {
//                         background-color: #f1f1f1;
//                     }

//                     #mqcz-popup-overlay {
//                         position: fixed;
//                         top: 0;
//                         left: 0;
//                         width: 100%;
//                         height: 100%;
//                         background: rgba(0, 0, 0, 0.5);
//                         z-index: 9998;
//                     }
//                 </style>
//                 <?php
//                 // Delete the transient after showing the message
//                 delete_transient('mqcz_first_special_occasions_popup');
//             }
//         }
//     }
// }
// add_action('admin_footer', 'mqcz_display_first_post_popup');

// function mqcz_handle_delete_special_occasions() {
//     if ( isset($_GET['action']) && $_GET['action'] === 'delete_special_occasions' && isset($_GET['post']) ) {
//         $post_id = $_GET['post'];

//         if ( isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_special_occasions_' . $post_id) ) {
//             $post = get_post($post_id);
//             if ($post && $post->post_type === 'special_occasions' && current_user_can('delete_post', $post_id)) {
//                 wp_delete_post($post_id, true); 
//                 wp_redirect(site_url('/dashboard')); 
//                 exit;
//             }
//         }
//     }
// }
// add_action('admin_init', 'mqcz_handle_delete_special_occasions');