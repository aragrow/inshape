<?php
/* The WP_InShape_Save_Post class listens for the save_post action and, when a post of type inshape is saved, it triggers the 
generation of a fitness plan description using custom fields from the post. After generating the description, it updates the 
post's content. */

/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WP_InShape_Save_Post {

    /* Purpose: The constructor hooks the detect_user_saved_post_inshape method to the save_post action, which is executed whenever a post is saved.
    Priority: The method is triggered with priority 20 to ensure other post-related operations are completed first.
    Arguments: The save_post action provides three arguments:
    $post_ID: The ID of the post being saved.
    $post: The post object.
    $update: A boolean that indicates whether the post is being updated or created. */
    public function __construct() {

        // Hook into save_post to trigger description generation when a 'product' post is saved
        add_action('save_post', [$this,'detect_user_saved_post_inshape'], 20, 3);
    }


    /* Purpose: This method is triggered when a post is saved. It performs several checks before generating and updating the fitness plan description:
    Post Type Check: It checks whether the post type is inshape. If it's not, the function returns early.
    Autosave & Revision Check: Prevents execution if the post is an autosave or a revision.
    Blank Content Check: If the post already contains content, the function will not trigger the description generation.
    Current User Check: Ensures that the post is saved by the current user (not by WordPress itself).
    Custom Field Processing: It loops through the custom fields and collects those starting with inshape_, then passes them as attributes to the API.
    API Call: It calls generate_plan_description from the WP_InShape_API_Integration class to generate a fitness plan description based on the title and attributes.
    Post Content Update: If the API response is successful, it updates the post content with the generated description using wp_update_post().
    If the API response fails, an error message is displayed. */
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

    /* Purpose: Converts the fitness plan description into a Gutenberg block (specifically a core/paragraph block).
    Block Structure: The function creates a block array with the following properties:
        blockName: Specifies the type of block (in this case, core/paragraph).
    attrs: An array for any block attributes (currently empty).
        innerContent: Contains the content of the block, which is the fitness plan description.
    Conversion: The block array is encoded into JSON and wrapped in the required Gutenberg block structure.
    Returns: The function returns the block HTML, which can be used to update the post content in a block-based editor.
    Note: Although this function is defined, it is not currently being used in the detect_user_saved_post_inshape method. 
        This function could be used if the content needs to be inserted as a Gutenberg block. */
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

/* Purpose: This line instantiates the WP_InShape_Save_Post class. */
new WP_InShape_Save_Post();
