<?php
namespace ataylorme\recipes_cpt;

/**
 * Register Recipes Custom Post Type
 */
function recipe_cpt() {

	$labels = array(
		'name'                  => _x( 'Recipes', 'Post Type General Name', 'ataylorme' ),
		'singular_name'         => _x( 'Recipe', 'Post Type Singular Name', 'ataylorme' ),
		'menu_name'             => __( 'Recipes', 'ataylorme' ),
		'name_admin_bar'        => __( 'Recipes', 'ataylorme' ),
		'archives'              => __( 'Recipes', 'ataylorme' ),
		'parent_item_colon'     => __( 'Parent Recipe:', 'ataylorme' ),
		'all_items'             => __( 'All Recipes', 'ataylorme' ),
		'add_new_item'          => __( 'Add New Recipe', 'ataylorme' ),
		'add_new'               => __( 'Add New', 'ataylorme' ),
		'new_item'              => __( 'New Recipe', 'ataylorme' ),
		'edit_item'             => __( 'Edit Recipe', 'ataylorme' ),
		'update_item'           => __( 'Update Recipe', 'ataylorme' ),
		'view_item'             => __( 'View Recipe', 'ataylorme' ),
		'search_items'          => __( 'Search Recipes', 'ataylorme' ),
		'not_found'             => __( 'No recipes found', 'ataylorme' ),
		'not_found_in_trash'    => __( 'No recipes found in Trash', 'ataylorme' ),
		'featured_image'        => __( 'Featured Image', 'ataylorme' ),
		'set_featured_image'    => __( 'Set featured image', 'ataylorme' ),
		'remove_featured_image' => __( 'Remove featured image', 'ataylorme' ),
		'use_featured_image'    => __( 'Use as featured image', 'ataylorme' ),
		'insert_into_item'      => __( 'Insert into recipe', 'ataylorme' ),
		'uploaded_to_this_item' => __( 'Uploaded to this recipe', 'ataylorme' ),
		'items_list'            => __( 'Items list', 'ataylorme' ),
		'items_list_navigation' => __( 'Items list navigation', 'ataylorme' ),
		'filter_items_list'     => __( 'Filter items list', 'ataylorme' ),
	);

	$args = array(
		'label'               => __( 'Recipe', 'ataylorme' ),
		'description'         => __( 'Food Recipes', 'ataylorme' ),
		'labels'              => $labels,
		'supports'            => array(
			'title', /*'editor', 'excerpt',*/
			'author',
			'thumbnail',
		),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => 'recipes',
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => false,
		'capability_type'     => 'page',
	);
	register_post_type( 'recipe', $args );

}

add_action( 'init', __NAMESPACE__ . '\recipe_cpt', 0 );