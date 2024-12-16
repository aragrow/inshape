<?php

/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WP_InShape_Client_menu_Admin_UI {

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
            'Client Tracker',
            'manage_options',
            'client-main-menu',
            [$this, 'render_admin_screen'],
            'dashicons-groups'
        );
    }

    public static function render_admin_screen() {
        echo '<div class="wrap">';
        echo '<h1>Client    racker</h1>';

        self::render_screen();

        echo '</div>';
    }

    private static function render_screen() {
        echo '<h2>Clients</h2>';

        $client = new WP_InShape_Client_CRUD();

        // Add client form
        echo '<form method="post" action="" style="margin-bottom: 20px;">';
        echo '<div style="display: flex; gap: 10px; flex-wrap: wrap;">';
        echo '<input type="text" name="client_name" placeholder="Name" required style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">';
        echo '<input type="email" name="client_email" placeholder="Email" required style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">';
        echo '<input type="text" name="client_phone" placeholder="Phone" style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">';
        echo '<button type="submit" name="add_client" style="padding: 10px 20px; background-color: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Client</button>';
        echo '</div>';
        echo '</form>';

        // Handle client creation
        if (isset($_POST['add_client'])) {
            $client->create_client($_POST['client_name'], $_POST['client_email'], $_POST['client_phone']);
            echo '<p>Client added successfully!</p>';
        }

        // Display clients
        $clients = $client->get_clients();

        echo '<div class="table-responsive">';
        echo '<table class="table table-striped" style="width: 100%; border-collapse: collapse;">';
        echo '<thead style="background-color: #f4f4f4;">';
        echo '<tr style="text-align: left;">';
        echo '<th style="padding: 10px; border-bottom: 2px solid #ddd;">Name</th>';
        echo '<th style="padding: 10px; border-bottom: 2px solid #ddd;">Email</th>';
        echo '<th style="padding: 10px; border-bottom: 2px solid #ddd;">Phone</th>';
        echo '<th style="padding: 10px; border-bottom: 2px solid #ddd;">Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($clients as $client) {
            echo '<tr>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #ddd;">' . esc_html($client->name) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #ddd;">' . esc_html($client->email) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #ddd;">' . esc_html($client->phone) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #ddd;"><a href="?page=cat-main-menu&page_type=athlete&client_id=' . esc_attr($client->id) . '" style="color: #0073aa; text-decoration: none;">Manage Athletes</a></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

}

/*
Purpose: Creates an instance of the WP_InShape_Client_menu_Admin_UI class.
*/
new WP_InShape_Client_menu_Admin_UI();