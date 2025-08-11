<?php 
require_once CHALLAN_PLUGIN_DIR . 'includes/classes/class_database.php';

class Activation {

    public function challan_search_activate(){

        /**
         * Initialize Database
         * @since 1.0.0
         */
        $db = new Database();
        $db->challan_search_create();

        if (!wp_next_scheduled('update_challan_status_event')) {
            wp_schedule_event(time(), 'daily', 'update_challan_status_event');
        }

    }
    public function challan_search_deactivate(){

        /**
         * Remove Database
         * @since 1.0.0
         */
        $db = new Database();
        $db->challan_search_remove();

        wp_clear_scheduled_hook('update_challan_status_event');
    }
}
?>