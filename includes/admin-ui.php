<?php
/*
The plugin provides a settings page where the administrator can enter an API URL and API key for interacting with the Gemini Google API. 
The code uses WordPress hooks and functions to create an options page, register settings, and display the settings form.
*/

/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/*
Purpose: Defines the main class for the plugin’s admin interface. This class handles the admin menu creation, 
settings registration, and the display of the settings page.
*/
class WP_InShape_Admin_UI {
    
    public $data_path;
    /*
    Purpose: The constructor initializes the plugin by hooking two functions into WordPress actions:
    admin_menu: Adds a menu item to the WordPress admin menu.
    admin_init: Registers the plugin settings.
    */
    public function __construct() {

        // Add admin menu
        add_action('admin_menu', [$this, 'product_add_admin_menu']);

        // Register settings
        add_action('admin_init', [$this, 'product_settings_init']);
    }


    /*
    Purpose: Adds a new options page to the WordPress admin interface under Settings.
        Page Title: 'InShape AI'
        Menu Title: 'InShape AI'
        Capability: manage_options (only accessible by users with administrative privileges)
        Menu Slug: inshape_AI
        Callback Function: inshape_settings_page (this function will render the settings page)
    */
    function product_add_admin_menu() {
        add_options_page(
            'InShape AI',
            'InShape AI',
            'manage_options',
            'inshape_AI',
            [$this, 'inshape_settings_page']
        );
    }


    /*
    Purpose: Registers two settings for the plugin:
    inshape_gemini_api_url: A setting to store the Gemini API URL.
    inshape_gemini_api_key: A setting to store the Gemini API key.
    Both settings are grouped under inshape_settings, which is the settings group name.
    */
    function product_settings_init() {
        register_setting('inshape_settings', 'inshape_gemini_api_url');
        register_setting('inshape_settings', 'inshape_gemini_api_key');
    }

    /*
    Purpose: Displays the settings page form.
    The page includes two fields for entering the API URL and API Key required for the Gemini Google API.
    settings_fields('inshape_settings'): This function outputs a nonce field and settings group for security and validation.
    do_settings_sections('inshape_settings'): This function renders any sections or fields added to the settings page, though 
        no additional sections are defined here.
    submit_button(): Renders the "Save Changes" button.
    Form uses POST method and options.php action to handle settings submission.
    */
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

        <h3>Generate 200 random import records with clients name and data points.</h3>
        <form>
            <button type="button" id="generate-csv-button" class="button button-primary">Generate CSV File</button>
        </form>
        <?php echo $this->display_files_in_table() ?>
        <script>
            // enqueue this script in your plugin
            jQuery(document).ready(function($) {
                $('#generate-csv-button').click(function() {
                    event.preventDefault(); // Prevent default form submission
                    $.ajax({
                        url: ajaxurl, // WordPress AJAX URL
                        type: 'POST',
                        data: {
                            action: 'inshape_generate_csv', //Your AJAX action
                            nonce: '<?php echo wp_create_nonce( 'inshape_generate_csv_nonce_76543' ); ?>', //Security nonce
                        },
                        success: function(response) {
                            // Handle the successful response from the server.
                            if (response.success) {
                                alert(JSON.stringify(response.data));
                                // Optional: provide a link to download the file
                                // ...
                            } else {
                                alert('Error generating CSV file: ' + response.data);
                            }
                        },
                        error: function(error) {
                            // Handle errors during the AJAX call.
                            console.error('AJAX error:', error);
                            alert('An error occurred while generating the file.\n'+error);
                        },
                    });
                });

                $('.import-csv-button').click(function() {
                    event.preventDefault(); // Prevent default form submission
                    file =$(this).data('filename')
                    $.ajax({
                            url: ajaxurl, // WordPress AJAX URL
                            type: 'POST',
                            data: {
                                action: 'inshape_import_csv', //Your AJAX action
                                nonce: '<?php echo wp_create_nonce( 'inshape_import_csv_nonce_28017' ); ?>', //Security nonce
                                csv_file: file,
                            },
                            success: function(response) {
                                // Handle the successful response from the server.
                                if (response.success) {
                                    alert(JSON.stringify(response.data));
                                    // Optional: provide a link to download the file
                                    // ...
                                } else {
                                    alert('Error importing CSV file: ' + response.data);
                                }
                            },
                            error: function(error) {
                                // Handle errors during the AJAX call.
                                console.error('AJAX error:', error);
                                alert('An error occurred while importing the file.\n'+error);
                            },
                        }); 
                });
            });
            
        </script>
    <?php }

    function display_files_in_table() {

        $files = scandir( INSHAPE_DATA_DIR );
        if ( ! $files ) {
            return '<p>Error: Could not read directory contents.</p>';
        }

        $table_html = '<br/><table class="wp-list-table widefat fixed striped">';
        $table_html .= '<thead><tr><th>Filename</th><th>Import</th></tr></thead>';
        $table_html .= '<tbody>';

        foreach ( $files as $file ) {
            if ( $file === '.' || $file === '..' ) {
                continue; //Skip current and parent directory entries
            }

            $filepath = esc_url( $this->data_path); //sanitize before using in href

            $table_html .= '<tr>';
            $table_html .= '<td>' . esc_html( $file ) . '</td>'; //Sanitize before displaying
            $table_html .= '<td><button type="button" class="button import-csv-button" data-filename="'. INSHAPE_DATA_DIR . esc_attr( $file ) . '">Import Data File</button></td>';
            $table_html .= '</tr>';
        }

        $table_html .= '</tbody></table>';

        return $table_html;
    }

}

/*
Purpose: Creates an instance of the WP_InShape_Admin_UI class.
*/
new WP_InShape_Admin_UI();