<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WP_InShape_Save_Post {

    public function __construct() {

        // Hook into save_post to trigger description generation when a 'product' post is saved
        add_action('save_post', [$this,'detect_user_saved_post_inshape'], 20, 3);
    }


    // Hook to detect post save for custom post type "product"
    function detect_user_saved_post_inshape( $post_ID, $post, $update) {

        // Check post type
        if ( get_post_type( $post_ID ) != 'inshape' ) {
            return;
        }

        error_log("Exec->detect_user_saved_post_inshape()");

        // Check if the post is autosave or revision (saved by the system)
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return;
        }

        // Check if the post is a revision (saved by the system)
        if ( wp_is_post_revision( $post_ID ) ) {
            return;
        }

        
        // Prevent to execute when the post_content is not blank
        if ( !empty($post->post_content) ) {
            return;
        }

        // Check if the post was saved by the user (not a system-generated save)
        if ( isset($_POST['post_author']) && $_POST['post_author'] == get_current_user_id() ) {
            
            $custom_fields = get_post_meta( $post_ID );
            $attributes= '';
        
            // Loop through the custom fields and display them
            foreach ( $custom_fields as $key => $value ) {
                if (strpos($key, 'inshape_') === 0) 
                    $attributes .= $key . ':' . implode( ', ', $value ) . '\n';
            }
        
            $api_response = (new WP_InShape_API_Integration)->generate_plan_description( $post->post_title, $attributes );
            // error_log('api_response: '.print_r($api_response['anwser'], true));

            // Update the post content
            if($api_response['status']) {
                
                $description = wp_kses_post( $api_response['anwser'] ); // If description contains HTML
                //$description = $this->convert_text_to_block( $description );
                $update = wp_update_post( [
                    'ID'           => $post_ID,
                    'post_content' => $description,
                ]);

            } else {
                
                echo 'No featured image set for this post.';
            
            }
            
        }
    }

    function convert_text_to_block( $description ) {
    
        // Convert text to a core/paragraph block
        $block = array(
            'blockName' => 'core/paragraph',
            'attrs' => array(), // Add attributes if needed
            'innerContent' => array(
                array(
                    'text' => $description,
                )
            )
        );
    
        // Convert the block array to JSON
        $block_json = wp_json_encode( $block );
    
        // Wrap the JSON in a valid Gutenberg block structure
        $new_content = '<!-- wp:' . esc_attr($block['blockName']) . ' -->' . $block_json . '<!-- /wp:' . esc_attr($block['blockName']) . ' -->';
    
        error_log($new_content);
    
        //Update the post content
        return $new_content;
    }

}

new WP_InShape_Save_Post();
