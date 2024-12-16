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

class WP_InShape_Athlete_CRUD 
{

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'cat_athletes';
    }

    // Create a new athlete
    public function create_athlete($client_id, $name, $sport) {
        global $wpdb;

        $wpdb->insert(
            $this->table_name,
            [
                'client_id' => $client_id,
                'name' => $name,
                'sport' => $sport,
            ],
            [
                '%d', // Integer for client_id
                '%s', // String for name
                '%s', // String for sport
            ]
        );

        return $wpdb->insert_id;
    }

    // Retrieve all athletes
    public function get_athletes() {
        global $wpdb;

        $query = "SELECT * FROM {$this->table_name}";
        return $wpdb->get_results($query);
    }

    // Retrieve all athletes for a specific client
    public function get_athletes_by_client($client_id) {
        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE client_id = %d", $client_id);
        return $wpdb->get_results($query);
    }

    // Retrieve a single athlete by ID
    public function get_athlete($id) {
        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id);
        return $wpdb->get_row($query);
    }

    // Update an athlete's information
    public function update_athlete($id, $name, $sport) {
        global $wpdb;

        return $wpdb->update(
            $this->table_name,
            [
                'name' => $name,
                'sport' => $sport,
            ],
            ['id' => $id],
            [
                '%s', // String for name
                '%s', // String for sport
            ],
            ['%d'] // Integer for ID
        );
    }

    // Delete an athlete
    public function delete_athlete($id) {
        global $wpdb;

        return $wpdb->delete(
            $this->table_name,
            ['id' => $id],
            ['%d'] // Integer for ID
        );
    }

}
