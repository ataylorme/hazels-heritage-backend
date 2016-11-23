<?php
namespace hazels_heritage\recipe_main_ingredient_tax;

/**
 * Register recipe main ingredient taxonomy
 */
function recipe_main_ingredient_tax() {

	$labels = array(
		'name'                       => _x( 'Recipe Main Ingredients', 'Recipe Main Ingredient Plural', 'hazels_heritage' ),
		'singular_name'              => _x( 'Recipe Main Ingredient', 'Recipe Main Ingredient Singular', 'hazels_heritage' ),
		'menu_name'                  => __( 'Main Ingredients', 'hazels_heritage' ),
		'all_items'                  => __( 'All', 'hazels_heritage' ),
		'parent_item'                => __( 'Parent Recipe Main Ingredient', 'hazels_heritage' ),
		'parent_item_colon'          => __( 'Parent Recipe Main Ingredient:', 'hazels_heritage' ),
		'new_item_name'              => __( 'New Recipe Main Ingredient Name', 'hazels_heritage' ),
		'add_new_item'               => __( 'Add New Recipe Main Ingredient', 'hazels_heritage' ),
		'edit_item'                  => __( 'Edit Recipe Main Ingredient', 'hazels_heritage' ),
		'update_item'                => __( 'Update Recipe Main Ingredient', 'hazels_heritage' ),
		'view_item'                  => __( 'View Recipe Main Ingredient', 'hazels_heritage' ),
		'separate_items_with_commas' => __( 'Separate recipe main ingredients with commas', 'hazels_heritage' ),
		'add_or_remove_items'        => __( 'Add or remove recipe main ingredients', 'hazels_heritage' ),
		'choose_from_most_used'      => __( 'Choose from the most used recipe main ingredients', 'hazels_heritage' ),
		'popular_items'              => __( 'Popular Recipe Main Ingredients', 'hazels_heritage' ),
		'search_items'               => __( 'Search Recipe Main Ingredients', 'hazels_heritage' ),
		'not_found'                  => __( 'No Recipe Main Ingredients Found', 'hazels_heritage' ),
		'no_terms'                   => __( 'No recipe main ingredients', 'hazels_heritage' ),
		'items_list'                 => __( 'Recipe Main Ingredients list', 'hazels_heritage' ),
		'items_list_navigation'      => __( 'Recipe Main Ingredients list navigation', 'hazels_heritage' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_menu'      => true,
		'show_in_nav_menus' => false,
		'show_tagcloud'     => false,
		'rewrite'           => false,
		'capabilities' => array(
			'assign_terms' => 'edit_recipes',
			'manage_terms' => 'manage_categories',
			'edit_terms' => 'manage_categories',
			'delete_terms' => 'manage_categories',
		),
		/**
		 * Removes metabox from post edit screen but
		 * keeps the admin UI for managing the taxonomy
		 */
		'meta_box_cb'       => false,
	);

	register_taxonomy( 'recipe_main_ingredient', array( 'recipe' ), $args );

}

add_action( 'init', __NAMESPACE__ . '\recipe_main_ingredient_tax', 0 );
