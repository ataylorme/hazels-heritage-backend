<?php
namespace hazels_heritage\recipes_cpt;

/**
 * Plugin Name: Recipe CPT
 * Plugin Author: Andrew Taylor
 * Plugin Description: Registers the recipe CPT
 */

$includes_dir = plugin_dir_path( __FILE__ ) . 'includes';

foreach ( glob( $includes_dir . '/*.php' ) as $filename ) {
	require_once( $filename );
}


/**
 * Activation hook
 * Flush rewrite rules for rest routes
 */

function activate() {
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activate' );