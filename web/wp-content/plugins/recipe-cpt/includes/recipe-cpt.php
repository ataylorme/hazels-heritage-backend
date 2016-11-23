<?php
namespace hazels_heritage\recipes_cpt;

/**
 * Register Recipes Custom Post Type
 */
function recipe_cpt() {

	$labels = array(
		'name'                  => _x( 'Recipes', 'Post Type General Name', 'hazels_heritage' ),
		'singular_name'         => _x( 'Recipe', 'Post Type Singular Name', 'hazels_heritage' ),
		'menu_name'             => __( 'Recipes', 'hazels_heritage' ),
		'name_admin_bar'        => __( 'Recipes', 'hazels_heritage' ),
		'archives'              => __( 'Recipes', 'hazels_heritage' ),
		'parent_item_colon'     => __( 'Parent Recipe:', 'hazels_heritage' ),
		'all_items'             => __( 'All Recipes', 'hazels_heritage' ),
		'add_new_item'          => __( 'Add New Recipe', 'hazels_heritage' ),
		'add_new'               => __( 'Add New', 'hazels_heritage' ),
		'new_item'              => __( 'New Recipe', 'hazels_heritage' ),
		'edit_item'             => __( 'Edit Recipe', 'hazels_heritage' ),
		'update_item'           => __( 'Update Recipe', 'hazels_heritage' ),
		'view_item'             => __( 'View Recipe', 'hazels_heritage' ),
		'search_items'          => __( 'Search Recipes', 'hazels_heritage' ),
		'not_found'             => __( 'No recipes found', 'hazels_heritage' ),
		'not_found_in_trash'    => __( 'No recipes found in Trash', 'hazels_heritage' ),
		'featured_image'        => __( 'Featured Image', 'hazels_heritage' ),
		'set_featured_image'    => __( 'Set featured image', 'hazels_heritage' ),
		'remove_featured_image' => __( 'Remove featured image', 'hazels_heritage' ),
		'use_featured_image'    => __( 'Use as featured image', 'hazels_heritage' ),
		'insert_into_item'      => __( 'Insert into recipe', 'hazels_heritage' ),
		'uploaded_to_this_item' => __( 'Uploaded to this recipe', 'hazels_heritage' ),
		'items_list'            => __( 'Items list', 'hazels_heritage' ),
		'items_list_navigation' => __( 'Items list navigation', 'hazels_heritage' ),
		'filter_items_list'     => __( 'Filter items list', 'hazels_heritage' ),
	);

	$args = array(
		'label'               => __( 'Recipe', 'hazels_heritage' ),
		'description'         => __( 'Food Recipes', 'hazels_heritage' ),
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
		'menu_icon'           => 'dashicons-carrot',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => 'recipes',
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => false,
		'capabilities'        => array(
			'edit_post'          => 'edit_recipe',
			'read_post'          => 'read_recipe',
			'delete_post'        => 'delete_recipe',
			'edit_posts'         => 'edit_recipes',
			'edit_others_posts'  => 'edit_others_recipes',
			'publish_posts'      => 'publish_recipes',
			'read_private_posts' => 'read_private_recipes',
			'create_posts'       => 'edit_recipes',
		),
		'map_meta_cap'        => true,
	);
	register_post_type( 'recipe', $args );

}

add_action( 'init', __NAMESPACE__ . '\recipe_cpt', 0 );