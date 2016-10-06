<?php
namespace ataylorme;

/**
 * This is a decoupled app
 * there is no WordPress front-end
 * so we'll redirect all traffic to the admin
 */
function redirect_to_admin() {

	if ( ! is_admin() ) {
		wp_redirect( get_admin_url() );
		exit();
	}

}

add_action( 'template_redirect', __NAMESPACE__ . '\redirect_to_admin', 99 );

/**
 * Setup theme
 */
function setup_theme() {
	/**
	 * Add theme support for post-thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\setup_theme' );