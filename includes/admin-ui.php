<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WP_InShape_Admin_UI {

    public function __construct() {

        // Add admin menu
        add_action('admin_menu', [$this, 'product_add_admin_menu']);

        // Register settings
        add_action('admin_init', [$this, 'product_settings_init']);
    }

    // Add the plugin settings page
    function product_add_admin_menu() {
        add_options_page(
            'InShape AI',
            'InShape AI',
            'manage_options',
            'inshape_AI',
            [$this, 'inshape_settings_page']
        );
    }

    // Register plugin settings
    function product_settings_init() {
        register_setting('inshape_settings', 'inshape_gemini_api_url');
        register_setting('inshape_settings', 'inshape_gemini_api_key');
    }

    // Display the settings page
    function inshape_settings_page() { ?>
        <div class="wrap">
        <h1>InShape AI Settings</h1>
            <form method="post" action="options.php">
                <h2>Gemini Google API</h1>
                <?php settings_fields('inshape_settings'); ?>
                <?php do_settings_sections('inshape_settings'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="inshape_gemini_api_url">API URL</label></th>
                        <td><input type="text" class="widefat" name="inshape_gemini_api_url" id="inshape_gemini_api_url"
                                value="<?php echo esc_attr(get_option('inshape_gemini_api_url', '')); ?>" 
                                class="regular-text" required></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="inshape_gemini_api_key">API Key</label></th>
                        <td><input type="text" class="widefat" name="inshape_gemini_api_key" id="inshape_gemini_api_key" 
                                value="<?php echo esc_attr(get_option('inshape_gemini_api_key', '')); ?>" 
                                class="regular-text" required></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

        </div>
    <?php }

}

new WP_InShape_Admin_UI();