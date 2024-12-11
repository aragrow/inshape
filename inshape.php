<?php
/*
Plugin Name: InShape
Description: A plugin to describe that helps you get in shape and achive your weight loss goals
Version: 1.1
Author: David Arago - ARAGROW, LLC
Author URI: https://aragrow.me
*/

// Ensure the genai library is installed and the gemini-pro-vision model is accessible.
// Replace "Write a short, engaging blog post based on this picture" with the specific prompt for your use case.

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/api-integration.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-fields.php';
require_once plugin_dir_path(__FILE__) . 'includes/save-post.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-ui.php';

function enqueue_inshape_meta_box_styles() {
    wp_enqueue_style( 'inshape-meta-box-styles', plugin_dir_url(__FILE__) . '/assets/css/styles.css' ); // Path to your CSS file
    wp_localize_script('inshape-script', 'inshapeAjax', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}
add_action( 'admin_enqueue_scripts', 'enqueue_inshape_meta_box_styles' );