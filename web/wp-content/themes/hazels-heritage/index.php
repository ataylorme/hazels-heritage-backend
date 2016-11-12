<?php
/**
 * This is a decoupled app
 * there is no WordPress front-end
 * so we'll redirect all traffic to the admin
 */
if ( ! is_admin() ) {
	wp_redirect( get_admin_url() );
	exit();
}