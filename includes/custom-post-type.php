<?php
/* The purpose of this class is to:

Register a custom post type (inshape).
Register a custom taxonomy (inshape-category), allowing posts to be categorized under different categories related to "InShape".
*/

/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if (!defined('ABSPATH')) exit;

/* Purpose: The constructor hooks the following actions:
init: Registers both the custom post type (inshape) and the custom taxonomy (inshape-category) when WordPress initializes. */
class WP_InShape_Register_Post_Type{

    /* Purpose: The constructor hooks the following actions:
    init: Registers both the custom post type (inshape) and the custom taxonomy (inshape-category) when WordPress initializes. */
    public function __construct() {
        
        // Register Custom Post Type
        add_action('init', [$this, 'register_inshape_post_type']);
        add_action( 'init', [$this, 'register_inshape_category_taxonomy'], 0 );
    
    }
    
    /* Purpose: Registers a custom post type (inshape) with specific configurations.
    Parameters:
    labels: Defines the labels for various actions such as adding a new item, editing an item, etc.
    public: Set to true, making the post type publicly accessible.
    show_in_rest: Set to true, enabling the custom post type to be accessible via the REST API (useful for the block editor).
    supports: Specifies the features that the post type supports. For inshape, it includes the title, editor, thumbnail, and custom fields (inshape-fields).
    taxonomies: Associates the custom taxonomy inshape-category with the inshape post type.
    has_archive: Enables an archive page for this post type.
    rewrite: Sets the URL slug for the custom post type (inshape). */
    function register_inshape_post_type() {
        
        register_post_type('inshape', [
            'labels' => [
                'name' => __('InShape'),
                'singular_name' => __('InShape'),
                'add_new_item' => __('Add New InShape'),
                'edit_item' => __('Edit InShape'),
                'new_item' => __('New InShape'),
                'view_item' => __('View InShape'),
                'all_items' => __('All InShape'),
            ],
            'public' => true,
            'show_in_rest' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'inshape-fields'],
            'taxonomies' => ['inshape-category'], // Enable categories
            'has_archive' => true,
            'rewrite' => ['slug' => 'inshape'],
        ]);

    }

    /* Purpose: Registers the custom taxonomy (inshape-category) for categorizing inshape posts.
    Parameters:
    labels: Defines the labels for the taxonomy, including options to add, edit, and view categories.
    hierarchical: Set to true to create a hierarchy of categories (like WordPress default categories).
    public: Makes the taxonomy publicly visible.
    show_ui: Enables the taxonomy's UI in the admin interface.
    show_in_nav_menus: Allows the taxonomy to be shown in navigation menus.
    show_in_rest: Exposes the taxonomy to the REST API for use in the block editor.
    rewrite: Sets the URL slug for the taxonomy (inshape-category). */

    function register_inshape_category_taxonomy() {
        $labels = array(
            'name'                       => _x( 'InShape Categories', 'Taxonomy General Name', 'textdomain' ),
            'singular_name'              => _x( 'InShape Category', 'Taxonomy Singular Name', 'textdomain' ),
            'menu_name'                  => __( 'InShape Categories', 'textdomain' ),
            'all_items'                  => __( 'All InShape Categories', 'textdomain' ),
            'parent_item'                => __( 'Parent InShape Category', 'textdomain' ),
            'parent_item_colon'          => __( 'Parent InShape Category:', 'textdomain' ),
            'new_item_name'              => __( 'New InShape Category Name', 'textdomain' ),
            'add_new_item'               => __( 'Add New InShape Category', 'textdomain' ),
            'edit_item'                  => __( 'Edit InShape Category', 'textdomain' ),
            'update_item'                => __( 'Update InShape Category', 'textdomain' ),
            'view_item'                  => __( 'View InShape Category', 'textdomain' ),
            'separate_items_with_commas' => __( 'Separate inshape categories with commas', 'textdomain' ),
            'add_or_remove_items'        => __( 'Add or remove inshape categories', 'textdomain' ),
            'choose_from_most_used'      => __( 'Choose from the most used inshape categories', 'textdomain' ),
            'popular_items'              => __( 'Popular InShape Categories', 'textdomain' ),
            'search_items'               => __( 'Search InShape Categories', 'textdomain' ),
            'not_found'                  => __( 'Not Found', 'textdomain' ),
            'no_terms'                   => __( 'No inshape categories', 'textdomain' ),
            'items_list'                 => __( 'InShape Categories list', 'textdomain' ),
            'items_list_navigation'      => __( 'InShape Categories list navigation', 'textdomain' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true, // Set to true for hierarchical categories (like regular categories)
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_in_rest'               => true,
            'show_tagcloud'              => true,
            'rewrite'                    => array( 'slug' => 'inshape-category' ), //InShapeize the slug
        );
        register_taxonomy( 'inshape-category', array( 'inshape' ), $args ); //'inshape_post_type' needs to be your inshape post type's name

    }

}

/* Purpose: This line instantiates the WP_InShape_Register_Post_Type class. */
new WP_InShape_Register_Post_Type();