<?php
if (!defined('ABSPATH')) exit;

class WP_InShape_Register_Post_Type{

    public function __construct() {
        
        // Register Custom Post Type
        add_action('init', [$this, 'register_inshape_post_type']);
        add_action( 'init', [$this, 'register_inshape_category_taxonomy'], 0 );
    
    }
   
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
new WP_InShape_Register_Post_Type();