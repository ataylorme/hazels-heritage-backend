<?php
namespace ataylorme\recipe_main_ingredient_tax;

/**
 * Register recipe main ingredient taxonomy
 */
function recipe_main_ingredient_tax() {

	$labels = array(
		'name'                       => _x( 'Recipe Main Ingredients', 'Recipe Main Ingredient Plural', 'ataylorme' ),
		'singular_name'              => _x( 'Recipe Main Ingredient', 'Recipe Main Ingredient Singular', 'ataylorme' ),
		'menu_name'                  => __( 'Main Ingredients', 'ataylorme' ),
		'all_items'                  => __( 'All', 'ataylorme' ),
		'parent_item'                => __( 'Parent Recipe Main Ingredient', 'ataylorme' ),
		'parent_item_colon'          => __( 'Parent Recipe Main Ingredient:', 'ataylorme' ),
		'new_item_name'              => __( 'New Recipe Main Ingredient Name', 'ataylorme' ),
		'add_new_item'               => __( 'Add New Recipe Main Ingredient', 'ataylorme' ),
		'edit_item'                  => __( 'Edit Recipe Main Ingredient', 'ataylorme' ),
		'update_item'                => __( 'Update Recipe Main Ingredient', 'ataylorme' ),
		'view_item'                  => __( 'View Recipe Main Ingredient', 'ataylorme' ),
		'separate_items_with_commas' => __( 'Separate recipe main ingredients with commas', 'ataylorme' ),
		'add_or_remove_items'        => __( 'Add or remove recipe main ingredients', 'ataylorme' ),
		'choose_from_most_used'      => __( 'Choose from the most used recipe main ingredients', 'ataylorme' ),
		'popular_items'              => __( 'Popular Recipe Main Ingredients', 'ataylorme' ),
		'search_items'               => __( 'Search Recipe Main Ingredients', 'ataylorme' ),
		'not_found'                  => __( 'No Recipe Main Ingredients Found', 'ataylorme' ),
		'no_terms'                   => __( 'No recipe main ingredients', 'ataylorme' ),
		'items_list'                 => __( 'Recipe Main Ingredients list', 'ataylorme' ),
		'items_list_navigation'      => __( 'Recipe Main Ingredients list navigation', 'ataylorme' ),
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
		/**
		 * Removes metabox from post edit screen but
		 * keeps the admin UI for managing the taxonomy
		 */
		'meta_box_cb'       => false,
	);

	register_taxonomy( 'recipe_main_ingredient', array( 'recipe' ), $args );

}

add_action( 'init', __NAMESPACE__ . '\recipe_main_ingredient_tax', 0 );
