<?php

/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if (!defined('ABSPATH')) exit;

/* Purpose: The constructor hooks the following actions:
init: Registers both the custom post type (inshape) and the custom taxonomy (inshape-category) when WordPress initializes. */
class WP_InShape_Front_End_Athlete_Form{

    public function __construct() {

        add_action('admin_post_save_injury_data', [$this,'save_injury_data']);
       // add_action('admin_post_nopriv_save_injury_data', [$this,'save_injury_data']);
    
    }

    // Register the shortcode
    function injury_data_form_shortcode() {
        ob_start(); ?>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="save_injury_data">
            <h2>Athlete Information</h2>
            <label>Name:</label>
            <input type="text" name="athlete_name" required><br>

            <label>Age:</label>
            <input type="number" name="athlete_age" required><br>

            <label>Height (cm):</label>
            <input type="number" name="athlete_height" step="0.1"><br>

            <label>Weight (kg):</label>
            <input type="number" name="athlete_weight" step="0.1"><br>

            <label>Position/Role:</label>
            <input type="text" name="athlete_position"><br>

            <label>Primary Sport:</label>
            <input type="text" name="athlete_sport"><br>

            <label>Date of Form Submission:</label>
            <input type="date" name="submission_date" required><br>

            <h2>Injury History</h2>
            <label>Knee Injuries:</label><br>
            <input type="checkbox" name="injury_knee[]" value="acl_tear"> ACL Tear<br>
            <input type="checkbox" name="injury_knee[]" value="meniscus_tear"> Meniscus Tear<br>
            <input type="checkbox" name="injury_knee[]" value="knee_sprain"> Knee Sprain<br>
            <input type="checkbox" name="injury_knee[]" value="jumpers_knee"> Jumper's Knee<br>

            <label>Elbow Injuries:</label><br>
            <input type="checkbox" name="injury_elbow[]" value="tennis_elbow"> Tennis Elbow<br>
            <input type="checkbox" name="injury_elbow[]" value="golfers_elbow"> Golfer's Elbow<br>

            <!-- Add more injury sections as needed -->

            <h2>Training and Health Data</h2>
            <label>Average Weekly Training Hours:</label>
            <input type="number" name="training_hours" step="0.1"><br>

            <label>Recovery Time Between Games:</label>
            <input type="text" name="recovery_time"><br>

            <label>Current Fitness Level (1-10):</label>
            <input type="number" name="fitness_level" min="1" max="10"><br>

            <label>Use of Protective Gear:</label>
            <select name="protective_gear">
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select><br>

            <input type="submit" value="Submit">
        </form>

        <?php
        return ob_get_clean();
    }
    // add_shortcode('injury_data_form', 'injury_data_form_shortcode');  Short Cut to add injury form

    // Handle form submission
    function save_injury_data() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $athlete_data = [
                'name' => sanitize_text_field($_POST['athlete_name']),
                'age' => intval($_POST['athlete_age']),
                'height' => floatval($_POST['athlete_height']),
                'weight' => floatval($_POST['athlete_weight']),
                'position' => sanitize_text_field($_POST['athlete_position']),
                'sport' => sanitize_text_field($_POST['athlete_sport']),
                'submission_date' => sanitize_text_field($_POST['submission_date']),
                'injuries_knee' => isset($_POST['injury_knee']) ? array_map('sanitize_text_field', $_POST['injury_knee']) : [],
                'injuries_elbow' => isset($_POST['injury_elbow']) ? array_map('sanitize_text_field', $_POST['injury_elbow']) : [],
                'training_hours' => floatval($_POST['training_hours']),
                'recovery_time' => sanitize_text_field($_POST['recovery_time']),
                'fitness_level' => intval($_POST['fitness_level']),
                'protective_gear' => sanitize_text_field($_POST['protective_gear']),
            ];

            // Save the data (for demonstration purposes, we'll log it)
            error_log(print_r($athlete_data, true));

            wp_redirect(home_url());
            exit;
        }
    }

}