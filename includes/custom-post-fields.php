<?php
if (!defined('ABSPATH')) exit;

class WP_InShape_Register_Post_Fields{

    public function __construct() {
        
        // Register Inhape Post Type
        add_action( 'add_meta_boxes', [$this, 'add_inshape_meta_box'] );
        add_action( 'save_post', [$this, 'save_inshape_meta_box'] );
    
    }

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

    //Callback function to display the meta box
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

    //Save the meta box data
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
        if ( isset( $_POST['inshape_activity_description_field'] ) ) {
            update_post_meta( $post_id, 'inshape_activity_description_field', sanitize_text_field( $_POST['inshape_activity_description_field'] ) );
        }
        if ( isset( $_POST['inshape_goal_description_field'] ) ) {
            update_post_meta( $post_id, 'inshape_goal_description_field', sanitize_text_field( $_POST['inshape_goal_description_field'] ) );
        }

    }
    

}
new WP_InShape_Register_Post_Fields();