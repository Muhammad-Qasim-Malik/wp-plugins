<?php
function mq_redirect_get_redirects($table) {
    global $wpdb;

    $table_name = $wpdb->prefix . $table; 

    $query = $wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY id DESC",
        $per_page,
        $offset
    );

    $results = $wpdb->get_results($query);

    return $results ? $results : false;
}

function mq_redirect_get_redirects_by_id($table, $id) {
    global $wpdb;

    $table_name = $wpdb->prefix . $table; 

    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $id");

    if ($results) {
        return $results;  
    } else {
        return false;  
    }
}

function mq_redirect_delete_redirect() {

    if(isset($_GET['delete'])){

        $id = $_GET['delete'];

        global $wpdb;
        $table_name = $wpdb->prefix . 'redirects';

        $where = array('id' => $id);
        
        $result = $wpdb->delete($table_name, $where);

        wp_redirect(admin_url('admin.php?page=mq-redirect&deleted=1'));
        exit;
    }
    
}

add_action('init', 'mq_redirect_delete_redirect');

function mq_redirect_add_ajax() {


    global $wpdb;
    $table_name = $wpdb->prefix . 'redirects';


    $id = absint($_POST['id']);
    $old_url = esc_url_raw( $_POST['old_url'] );
    $new_url = esc_url_raw( $_POST['new_url'] );

    // error_log($id . $old_url . $new_url);
    $result  = $wpdb->insert(
                    $table_name,
                    array(
                        'old_url' => $old_url,
                        'new_url' => $new_url,
                    ),
                    array(
                        '%s',
                        '%s'
                    )
                );
    if($result){
        wp_send_json_success("Data Added Successfully"); 
    } else {
        wp_send_json_error("Faild to Add Data"); 
    }
    wp_die();
}

add_action('wp_ajax_mq_redirect_add_ajax', 'mq_redirect_add_ajax');
add_action('wp_ajax_nopriv_mq_redirect_add_ajax', 'mq_redirect_add_ajax');

function mq_redirect_update_ajax() {
    // error_log(print_r($_POST, true));
    // error_log('Hello');

    global $wpdb;
    $table_name = $wpdb->prefix . 'redirects';

    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error('Permission denied.');
    }

    $id = absint($_POST['id']);
    $old_url = esc_url_raw( $_POST['old_url'] );
    $new_url = esc_url_raw( $_POST['new_url'] );

    // error_log($id . $old_url . $new_url);
    $result  = $wpdb->update(
                    $table_name,
                    array(
                        'old_url' => $old_url,
                        'new_url' => $new_url,
                    ),
                    array(
                        'id' => $id,
                    )
                );
    if($result){
        wp_send_json_success("Data Updated Successfully"); 
    } else {
        wp_send_json_error("Faild to Update Data"); 
    }
    wp_die();
}

add_action('wp_ajax_mq_redirect_update_ajax', 'mq_redirect_update_ajax');
// add_action('wp_ajax_nopriv_mq_redirect_update_ajax', 'mq_redirect_update_ajax');

function mq_redirects_upload_ajax() {

    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error('Permission denied.');
    }

    require_once(ABSPATH . 'wp-admin/includes/file.php');
    $uploaded_file = $_FILES['file'];

    if ( ! empty($uploaded_file['error']) ) {
        wp_send_json_error(array('message' => 'Error uploading file.'));
    } 

    $tmp_name = $uploaded_file['tmp_name'];

    global $wpdb;
    $table_name = $wpdb->prefix . 'redirects';

    $inserted = 0;
    $skipped  = 0;
    $errors   = [];

    if ( ($handle = fopen($tmp_name, 'r')) === false ) {
        wp_send_json_error('Unable to open uploaded CSV');
    }

    $headers = fgetcsv($handle);
    if ( ! $headers ) {
        fclose($handle);
        wp_send_json_error('CSV is empty or headers missing');
    }

    while ( ($row = fgetcsv($handle)) !== false ) {
        if ( count($row) < 2 ) {
            $skipped++;
            continue;
        }

        $old_url = esc_url_raw(trim($row[0]));
        $new_url = esc_url_raw(trim($row[1]));

        if ( empty($old_url) || empty($new_url) ) {
            $skipped++;
            continue;
        }

        $result = $wpdb->insert(
            $table_name,
            array(
                'old_url' => $old_url,
                'new_url' => $new_url,
            ),
            array('%s', '%s')
        );

        if ( $result === false ) {
            $errors[] = "DB error for row: " . implode(',', $row);
        } else {
            $inserted++;
        }
    }

    fclose($handle);

    wp_send_json_success(array(
        'inserted' => $inserted,
        'skipped'  => $skipped,
        'errors'   => $errors,
    ));
}
add_action('wp_ajax_mq_redirects_upload_ajax', 'mq_redirects_upload_ajax');



function mq_handle_redirects() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'redirects';
    $uri      = $_SERVER['REQUEST_URI'];
    $uri = home_url($uri);

    $redirect = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT new_url FROM $table_name WHERE old_url = %s LIMIT 1",
            $uri
        )
    );
    // error_log($redirect->new_url);

    if ( $redirect && ! empty( $redirect->new_url ) ) {
        wp_redirect( $redirect->new_url, 301 );
        exit;
    }
}
add_action('template_redirect', 'mq_handle_redirects');
