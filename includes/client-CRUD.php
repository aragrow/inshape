<?php
/*
This class is responsible for integrating with the InShape API (likely the Gemini API) to generate fitness plans based on 
user data. It also includes a method for calling a local Python script that presumably performs image analysis.
*/

/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if (!defined('ABSPATH')) exit;

class WP_InShape_Client_CRUD {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'cat_clients';
    }

    // Create a new client
    public function create_client($name, $email, $phone) {
        global $wpdb;

        $wpdb->insert(
            $this->table_name,
            [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ],
            [
                '%s', // String for name
                '%s', // String for email
                '%s', // String for phone
            ]
        );

        return $wpdb->insert_id;
    }

    // Retrieve all clients
    public function get_clients() {
        global $wpdb;

        $query = "SELECT * FROM {$this->table_name}";
        return $wpdb->get_results($query);
    }

    // Retrieve a single client by ID
    public function get_client($id) {
        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id);
        return $wpdb->get_row($query);
    }

    // Update a client's information
    public function update_client($id, $name, $email, $phone) {
        global $wpdb;

        return $wpdb->update(
            $this->table_name,
            [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ],
            ['id' => $id],
            [
                '%s', // String for name
                '%s', // String for email
                '%s', // String for phone
            ],
            ['%d'] // Integer for ID
        );
    }

    // Delete a client
    public function delete_client($id) {
        global $wpdb;

        return $wpdb->delete(
            $this->table_name,
            ['id' => $id],
            ['%d'] // Integer for ID
        );
    }
}
