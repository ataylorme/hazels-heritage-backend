<?php
namespace ataylorme\recipes_cpt;

/**
 * Plugin Name: Recipe CPT
 * Plugin Author: Andrew Taylor
 * Plugin Description: Registers the recipe CPT
 */

$includes_dir = plugin_dir_path( __FILE__ ) . 'includes';

foreach ( glob( $includes_dir . '/*.php' ) as $filename ) {
	require_once( $filename );
}