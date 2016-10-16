<?php
namespace hazels_heritage\recipe_type_tax;

/**
 * Register recipe type taxonomy
 */
function recipe_type_tax() {

	$labels = array(
		'name'                       => _x( 'Recipe Types', 'Recipe Type Plural', 'hazels_heritage' ),
		'singular_name'              => _x( 'Recipe Type', 'Recipe Type Singular', 'hazels_heritage' ),
		'menu_name'                  => __( 'Recipe Types', 'hazels_heritage' ),
		'all_items'                  => __( 'All', 'hazels_heritage' ),
		'parent_item'                => __( 'Parent Recipe Type', 'hazels_heritage' ),
		'parent_item_colon'          => __( 'Parent Recipe Type:', 'hazels_heritage' ),
		'new_item_name'              => __( 'New Recipe Type Name', 'hazels_heritage' ),
		'add_new_item'               => __( 'Add New Recipe Type', 'hazels_heritage' ),
		'edit_item'                  => __( 'Edit Recipe Type', 'hazels_heritage' ),
		'update_item'                => __( 'Update Recipe Type', 'hazels_heritage' ),
		'view_item'                  => __( 'View Recipe Type', 'hazels_heritage' ),
		'separate_items_with_commas' => __( 'Separate recipe types with commas', 'hazels_heritage' ),
		'add_or_remove_items'        => __( 'Add or remove recipe types', 'hazels_heritage' ),
		'choose_from_most_used'      => __( 'Choose from the most used recipe types', 'hazels_heritage' ),
		'popular_items'              => __( 'Popular Recipe Types', 'hazels_heritage' ),
		'search_items'               => __( 'Search Recipe Types', 'hazels_heritage' ),
		'not_found'                  => __( 'No Recipe Types Found', 'hazels_heritage' ),
		'no_terms'                   => __( 'No recipe types', 'hazels_heritage' ),
		'items_list'                 => __( 'Recipe Types list', 'hazels_heritage' ),
		'items_list_navigation'      => __( 'Recipe Types list navigation', 'hazels_heritage' ),
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

	register_taxonomy( 'recipe_type', array( 'recipe' ), $args );

}

add_action( 'init', __NAMESPACE__ . '\recipe_type_tax', 0 );
