<?php

/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WP_InShape_Athlete_menu_Admin_UI {

    /*Purpose: The constructor initializes the plugin by hooking two functions into WordPress actions:
    admin_menu: Adds a menu item to the WordPress admin menu.
    admin_init: Registers the plugin settings.
    */
    public function __construct() {
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    function admin_menu() {
        add_menu_page(
            'Athlete Tracker',
            'Athlete Tracker',
            'manage_options',
            'athlete-main-menu',
            [$this, 'render_admin_screen'],
            'dashicons-groups'
        );
    }

    public static function render_admin_screen() {
        echo '<div class="wrap">';
        echo '<h1>Athlete Tracker</h1>';

        self::render_screen();

        echo '</div>';
    }

    private static function render_screen() {
        echo '<h2>Athletes</h2>';

        $client_id = intval($_GET['client_id']);

        $athlete  = new WP_InShape_Athlete_CRUD();

        // Fetch clients for dropdown
        $client = new WP_InShape_Client_CRUD();
        $clients = $client->get_clients();

        // Add athlete form
        echo '<form method="post" action="" style="margin-bottom: 20px;">';
        echo '<div style="display: flex; gap: 10px; flex-wrap: wrap;">';
        echo '<select name="client_id" required style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">';
        echo '<option value="" disabled selected>Select Client</option>';
        foreach ($clients as $client) {
            echo '<option value="' . esc_attr($client->id) . '">' . esc_html($client->name) . '</option>';
        }
        echo '</select>';
        echo '<input type="text" name="athlete_name" placeholder="Name" required style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">';
        echo '<input type="text" name="athlete_sport" placeholder="Sport" style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">';
        echo '<button type="submit" name="add_athlete" style="padding: 10px 20px; background-color: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Athlete</button>';
        echo '</div>';
        echo '</form>';


        // Handle athlete creation
        if (isset($_POST['add_athlete'])) {
            $athlete->create_athlete($client_id, $_POST['athlete_name'], $_POST['athlete_sport']);
            echo '<p>Athlete added successfully!</p>';
        }

        // Display athletes
        $athletes = $athlete->get_athletes_by_client($client_id);

        echo '<div class="table-responsive">';
        echo '<table class="table table-striped" style="width: 100%; border-collapse: collapse;">';
        echo '<thead style="background-color: #f4f4f4;">';
        echo '<tr style="text-align: left;">';
        echo '<th style="padding: 10px; border-bottom: 2px solid #ddd;">Name</th>';
        echo '<th style="padding: 10px; border-bottom: 2px solid #ddd;">Sport</th>';
        echo '<th style="padding: 10px; border-bottom: 2px solid #ddd;">Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($athletes as $athlete) {
            echo '<tr>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #ddd;">' . esc_html($athlete->name) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #ddd;">' . esc_html($athlete->sport) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #ddd;"><a href="?page=cat-main-menu&delete_athlete=' . esc_attr($athlete->id) . '" style="color: #d9534f; text-decoration: none;">Delete</a></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        // Handle athlete deletion
        if (isset($_GET['delete_athlete'])) {
            $athlete->delete_athlete(intval($_GET['delete_athlete']));
            echo '<p>Athlete deleted successfully!</p>';
        }
    }
}

/*
Purpose: Creates an instance of the WP_InShape_Athlete_menu_Admin_UI class.
*/
new WP_InShape_Athlete_menu_Admin_UI();
