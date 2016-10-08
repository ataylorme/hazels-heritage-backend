<?php
namespace ataylorme\recipe_type_tax;

/**
 * Register recipe type taxonomy
 */
function recipe_type_tax() {

	$labels = array(
		'name'                       => _x( 'Recipe Types', 'Recipe Type Plural', 'ataylorme' ),
		'singular_name'              => _x( 'Recipe Type', 'Recipe Type Singular', 'ataylorme' ),
		'menu_name'                  => __( 'Recipe Types', 'ataylorme' ),
		'all_items'                  => __( 'All', 'ataylorme' ),
		'parent_item'                => __( 'Parent Recipe Type', 'ataylorme' ),
		'parent_item_colon'          => __( 'Parent Recipe Type:', 'ataylorme' ),
		'new_item_name'              => __( 'New Recipe Type Name', 'ataylorme' ),
		'add_new_item'               => __( 'Add New Recipe Type', 'ataylorme' ),
		'edit_item'                  => __( 'Edit Recipe Type', 'ataylorme' ),
		'update_item'                => __( 'Update Recipe Type', 'ataylorme' ),
		'view_item'                  => __( 'View Recipe Type', 'ataylorme' ),
		'separate_items_with_commas' => __( 'Separate recipe types with commas', 'ataylorme' ),
		'add_or_remove_items'        => __( 'Add or remove recipe types', 'ataylorme' ),
		'choose_from_most_used'      => __( 'Choose from the most used recipe types', 'ataylorme' ),
		'popular_items'              => __( 'Popular Recipe Types', 'ataylorme' ),
		'search_items'               => __( 'Search Recipe Types', 'ataylorme' ),
		'not_found'                  => __( 'No Recipe Types Found', 'ataylorme' ),
		'no_terms'                   => __( 'No recipe types', 'ataylorme' ),
		'items_list'                 => __( 'Recipe Types list', 'ataylorme' ),
		'items_list_navigation'      => __( 'Recipe Types list navigation', 'ataylorme' ),
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
