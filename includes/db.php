<?php

register_activation_hook(__FILE__, 'inshape_create_tables');

function inshape_create_tables() {

    inshape_client_create_table();
    inshape_athlete_create_table();
    inshape_client_user_create_table();
}


function inshape_client_create_table() {
    global $wpdb;

    $table = $wpdb->prefix . 'inshape_clients';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table'")) return;

    $charset_collate = $wpdb->get_charset_collate();

    // SQL to create clients table
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function inshape_athlete_create_table() {
    global $wpdb;

    $table = $wpdb->prefix . 'inshape_athletes';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table'")) return;

    $client_table = $wpdb->prefix . 'inshape_clients';

    $charset_collate = $wpdb->get_charset_collate();

    // SQL to create athletes table
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        client_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        sport VARCHAR(255),
        FOREIGN KEY (client_id) REFERENCES $client_table(id) ON DELETE CASCADE
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function inshape_client_user_create_table() {
    global $wpdb;

    $table = $wpdb->prefix . 'inshape_clients_users';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table'")) return;

    $client_table = $wpdb->prefix . 'inshape_clients';
    $user_table = $wpdb->prefix . 'users';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE  IF NOT EXISTS $table (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        client_id BIGINT UNSIGNED NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (client_id) REFERENCES $client_table(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES $user_table(ID) ON DELETE CASCADE
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}