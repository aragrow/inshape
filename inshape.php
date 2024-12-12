<?php
/*
Plugin Name: InShape
Description: A plugin to describe that helps you get in shape and achive your weight loss goals
Version: 1.1
Author: David Arago - ARAGROW, LLC
Author URI: https://aragrow.me
*/

// Ensure the genai library is installed and the gemini-pro-vision model is accessible.

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define a constant for the plugin's base directory. This makes the code more readable and easier to maintain.
defined( 'INSHAPE_BASE_DIR' ) or define( 'INSHAPE_BASE_DIR', plugin_dir_path( __FILE__ ) );


// Define a constant for the data directory. This separates the path logic from its use, and makes it more maintainable.
defined( 'INSHAPE_DATA_DIR' ) or define( 'INSHAPE_DATA_DIR', INSHAPE_BASE_DIR . 'assets/sample-data/' );

require_once plugin_dir_path(__FILE__) . 'includes/api-integration.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-fields.php';
require_once plugin_dir_path(__FILE__) . 'includes/save-post.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-ui.php';
require_once plugin_dir_path(__FILE__) . 'includes/sample-data.php';

/* The function enqueue_inshape_meta_box_styles() is used to enqueue a custom CSS file (styles.css) and make an 
AJAX URL available to your JavaScript code. This is useful when you need to apply custom styling to the InShape 
meta box in the WordPress admin interface and also need to make AJAX calls in your custom JavaScript. */
function enqueue_inshape_meta_box_styles() {
    wp_enqueue_style( 'inshape-meta-box-styles', plugin_dir_url(__FILE__) . '/assets/css/styles.css' ); // Path to your CSS file
    wp_localize_script('inshape-script', 'inshapeAjax', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}
add_action( 'admin_enqueue_scripts', 'enqueue_inshape_meta_box_styles' );