<?php
/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if (!defined('ABSPATH')) exit;

/* The WP_InShape_Register_Post_Fields class registers a custom meta box for the inshape custom post type. It handles the following tasks:
Adds a meta box for collecting user data related to fitness.
Displays the form fields in the meta box for the user to fill out.
Saves the entered data to the post’s metadata when the post is saved. */
class WP_InShape_Register_Post_Fields{

    /* Purpose: The constructor hooks the following actions:
    add_meta_boxes: Registers a meta box for the inshape custom post type.
    save_post: Ensures that the custom fields are saved when the post is saved. */
    public function __construct() {
        
        // Register Inhape Post Type
        add_action( 'add_meta_boxes', [$this, 'add_inshape_meta_box'] );
        add_action( 'save_post', [$this, 'save_inshape_meta_box'] );
    
    }

    /* Purpose: Registers a meta box called "InShape Fields" that is added to posts of type inshape.
    Parameters:
    ID: The unique ID of the meta box (inshape_meta_box).
    Title: The title of the meta box (InShape Fields).
    Callback: The function that will render the meta box content (render_inshape_meta_box).
    Post Type: The post type for which the meta box is shown (inshape).
    Context: The location of the meta box (normal for the main content area).
    Priority: The display priority of the meta box (high for top priority). */
    public function add_inshape_meta_box() {
        add_meta_box(
            'inshape_meta_box', //Unique ID
            'InShape Fields', //Title
            [$this, 'render_inshape_meta_box'], //Callback function to display the meta box
            'inshape', //Post type
            'normal', //Context (side, normal, advanced)
            'high' //Priority (high, core, default, low)
        );
    }

    /* Purpose: Renders the fields in the meta box for the user to fill in the relevant information about their fitness profile.
    Functionality:
    It displays input fields.
    Each field retrieves and displays existing values (if any) from the post's metadata.
    It adds a nonce field to secure the form submission. */
    public function render_inshape_meta_box( $post ) {

        // Check post type
        if ( get_post_type( $post->ID ) != 'inshape' ) {
            return;
        }

        // Check if the post is autosave or revision (saved by the system)
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return;
        }

        // Check if the post is a revision (saved by the system)
        if ( wp_is_post_revision( $post->ID  ) ) {
            return;
        }
        // Add nonce field for security
        wp_nonce_field( 'inshape_meta_box', 'inshape_meta_box_nonce' );
    
        // Get existing values for the fields
        $ethnicity_value = get_post_meta( $post->ID, 'inshape_ethnicity_field', true );
        $gender_value = get_post_meta( $post->ID, 'inshape_gender_field', true );
        $age_value = get_post_meta( $post->ID, 'inshape_age_field', true );
        $weight_value = get_post_meta( $post->ID, 'inshape_weight_field', true );
        $units = get_post_meta( $post->ID, 'inshape_unit_field', true );
        $height_value = get_post_meta( $post->ID, 'inshape_height_field', true );
        $waist_value = get_post_meta( $post->ID, 'inshape_waist_field', true );
        $activity_value = get_post_meta( $post->ID, 'inshape_activity_field', true );
        $activity_desc = get_post_meta( $post->ID, 'inshape_activity_description_field', true );
        $goal_desc = get_post_meta( $post->ID, 'inshape_goal_description_field', true );
    
        ?>
        <div id="inshape_meta_box">  <!-- Added div with ID -->
            
            <label for="inshape_goal_description_field">Goal Description:<span>(Describe your goal in details)</span></label><br>
            <textarea class="custom-textbox" id="inshape_goal_description_field" name="inshape_goal_description_field" required><?php echo esc_attr( $goal_desc ); ?></textarea>

            <label for="inshape_ethnicity_field">Ethnicity:<span>(Multiple selection hold cntrl/cmd key and click on option)</span></label><br>
            <select id="inshape_ethnicity_field" name="inshape_ethnicity_field">
                <option value="Do not know" selected >Do not know</option>
                <optgroup label="Asian">
                    <option value="Filipino American" <?php selected( $ethnicity_value, 'Filipino American', true ); ?>>Filipino American</option>
                    <option value="Chinese American" <?php selected( $ethnicity_value, 'Chinese American', true ); ?>>Chinese American</option>
                    <option value="Japanese American" <?php selected( $ethnicity_value, 'Japanese American', true ); ?>>Japanese American</option>
                    <option value="Korean American" <?php selected( $ethnicity_value, 'Korean American', true ); ?>>Korean American</option>
                    <option value="Vietnamese American <?php selected( $ethnicity_value, 'Vietnamese American', true ); ?>">Vietnamese American</option>
                    <option value="Pacific Islander American <?php selected( $ethnicity_value, 'Pacific Islander American', true ); ?>">Pacific Islander American</option>  
                </optgroup>
                <optgroup label="Latin American/Hispanic/Caribbean">
                    <option value="Mexican American" <?php selected( $ethnicity_value, 'Mexican American', true ); ?>>Mexican American</option>
                    <option value="Puerto Rican American" <?php selected( $ethnicity_value, 'Puerto Rican American', true ); ?>>Puerto Rican American</option>
                    <option value="Cuban American" <?php selected( $ethnicity_value, 'Cuban American', true ); ?>>Cuban American</option>
                    <option value="Dominican American" <?php selected( $ethnicity_value, 'Dominican American"', true ); ?>>Dominican American</option>
                    <option value="Central American" <?php selected( $ethnicity_value, 'Central American', true ); ?>>Central American</option>  
                    <option value="South American" <?php selected( $ethnicity_value, 'South American', true ); ?>>South American</option>  
                    <option value="Caribbean American" <?php selected( $ethnicity_value, 'Caribbean American', true ); ?>>Caribbean American</option> 
                </optgroup>
                <optgroup label="European">
                    <option value="European American" <?php selected( $ethnicity_value, 'European American', true ); ?>>European American</option>  
                </optgroup>
                <optgroup label="Middle Eastern">
                    <option value="Middle Eastern American" <?php selected( $ethnicity_value, 'Middle Eastern American', true ); ?>>Middle Eastern American</option>  
                </optgroup>
                <optgroup label="North American">
                    <option value="White American" <?php selected( $ethnicity_value, 'White American', true ); ?>>White American</option>
                    <option value="African American" <?php selected( $ethnicity_value, 'African American', true ); ?>>African American</option>  
                </optgroup>
                <optgroup label="Other">
                    <option value="Other">Other</option>
                </optgroup>
            </select>

            <label for="inshape_gender_field">Gender:</label><br>
            <select id="inshape_gender_field" name="inshape_gender_field" required>
                <option value="" selected>Prefer not to say</option>
                <option value="male" <?php selected( $gender_value, 'male', true ); ?> >Male</option>
                <option value="female" <?php selected( $gender_value, 'female', true ); ?> >Female</option>
            </select>
    
            <label for="inshape_age_field">Age:</label><br>
            <input type="number" id="inshape_age_field" name="inshape_age_field" value="<?php echo esc_attr( $age_value ); ?>" size="10" min="18" max="150" required>

            <label for="inshape_units_field">Units:</label><br>
            <select id="inshape_units_field" name="inshape_units_field" required>
                <option value="imperial" selected>Imperial</option>
                <option value="metric" <?php selected( $units, 'metric', true ); ?> >Kilograms</option>
            </select>

            <label for="inshape_height_field">Height:</label><br>
            <input type="number" id="inshape_height_field" name="inshape_height_field" value="<?php echo esc_attr( $height_value ); ?>" size="10" min="1" max="300" required>

            <label for="inshape_age_field">Weight:</label><br>
            <input type="number" id="inshape_weight_field" name="inshape_weight_field" value="<?php echo esc_attr( $weight_value ); ?>" size="10" min="1" max="150" required>

            <label for="inshape_waist_field">Waist:<small>(Waist Circumference: Indicates abdominal fat, a strong predictor of health risks.)</small></label><br>
            <input type="number" id="inshape_waist_field" name="inshape_waist_field" value="<?php echo esc_attr( $waist_value ); ?>" size="10" min="1" max="300" required>

            <label for="inshape_activity_field">Physical Activity:</label><br>
            <select id="inshape_activity_field" name="inshape_activity_field">
                <option value="sedentary" <?php selected( $activity_value, 'sedentary', true ); ?>>Sedentary (Little or no exercise, mostly sitting)</option>
                <option value="lightly_active" <?php selected( $activity_value, 'lightly_active', true ); ?>>Lightly Active (Light exercise/sports 1-3 days/week, or light physical job)</option>
                <option value="moderately_active" selected >Moderately Active (Moderate exercise/sports 3-5 days/week)</option>
                <option value="very_active" <?php selected( $activity_value, 'very_active', true ); ?>>Very Active (Hard exercise/sports 6-7 days/week)</option>
                <option value="extra_active" <?php selected( $activity_value, 'extra_active', true ); ?>>Extra Active (Very hard exercise/sports & a physically demanding job)</option>
            </select>

            <label for="inshape_activity_description_field">Physical Activity Description:<span>(Describe your physical activity in details)</span></label><br>
            <textarea class="custom-textbox" id="inshape_activity_description_field" name="inshape_activity_description_field" required><?php echo esc_attr( $activity_desc ); ?></textarea>

        </div>
        <?php

    }

    /* Purpose: This method saves the custom field data entered in the meta box to the post’s metadata.
    Functionality:
    Security: The nonce field ensures that the request to save the data is valid.
    Permissions Check: The method verifies that the current user has permission to edit the post.
    Sanitization: The method sanitizes the data before saving it to the database to ensure security and data integrity.
    Saving Data: Each custom field is saved using update_post_meta() with the sanitized input. */
    public function save_inshape_meta_box( $post_id ) {

        // Check post type
        if ( get_post_type( $post_id ) != 'inshape' ) {
            return;
        }

        error_log("Exec->save_inshape_meta_box()");

        // Check nonce
        if ( ! isset( $_POST['inshape_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['inshape_meta_box_nonce'], 'inshape_meta_box' ) ) {
            return;
        }

        // Check permissions
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check capabilities
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Sanitize and save the data
        if ( isset( $_POST['inshape_ethnicity_field'] ) ) {
            update_post_meta( $post_id, 'inshape_ethnicity_field', sanitize_text_field( $_POST['inshape_ethnicity_field'] ) );
        }
        if ( isset( $_POST['inshape_gender_field'] ) ) {
            update_post_meta( $post_id, 'inshape_gender_field', sanitize_text_field( $_POST['inshape_gender_field'] ) );
        }
        if ( isset( $_POST['inshape_age_field'] ) ) {
            update_post_meta( $post_id, 'inshape_age_field', sanitize_textarea_field( $_POST['inshape_age_field'] ) );
        }
        if ( isset( $_POST['inshape_units_field'] ) ) {
            update_post_meta( $post_id, 'inshape_units_field', sanitize_text_field( $_POST['inshape_units_field'] ) );
        }
        if ( isset( $_POST['inshape_height_field'] ) ) {
            update_post_meta( $post_id, 'inshape_height_field', sanitize_textarea_field( $_POST['inshape_height_field'] ) );
        }
        if ( isset( $_POST['inshape_weight_field'] ) ) {
            update_post_meta( $post_id, 'inshape_weight_field', sanitize_text_field( $_POST['inshape_weight_field'] ) );
        }
        if ( isset( $_POST['inshape_waist_field'] ) ) {
            update_post_meta( $post_id, 'inshape_waist_field', sanitize_textarea_field( $_POST['inshape_waist_field'] ) );
        }
        if ( isset( $_POST['inshape_activity_field'] ) ) {
            update_post_meta( $post_id, 'inshape_activity_field', sanitize_textarea_field( $_POST['inshape_activity_field'] ) );
        }
        if ( isset( $_POST['inshape_activity_description_field'] ) ) {
            update_post_meta( $post_id, 'inshape_activity_description_field', sanitize_text_field( $_POST['inshape_activity_description_field'] ) );
        }
        if ( isset( $_POST['inshape_goal_description_field'] ) ) {
            update_post_meta( $post_id, 'inshape_goal_description_field', sanitize_text_field( $_POST['inshape_goal_description_field'] ) );
        }

    }
    
}

/* Purpose: This line instantiates the WP_InShape_Register_Post_Fields class. */
new WP_InShape_Register_Post_Fields();