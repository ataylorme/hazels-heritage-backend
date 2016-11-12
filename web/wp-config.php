<?php
/*
 * Don't show deprecations
 */
error_reporting( E_ALL ^ E_DEPRECATED );

/**
 * Set root path
 */
$rootPath = realpath( __DIR__ . '/..' );

/**
 * Include the Composer autoload
 */
require_once( $rootPath . '/vendor/autoload.php' );

/*
 * Fetch .env
 */
if ( file_exists( $rootPath . '/.env' ) ) {
	$dotenv = new Dotenv\Dotenv( $rootPath );
	$dotenv->load();
	$dotenv->required( array(
		'DB_NAME',
		'DB_USER',
		'DB_HOST',
		'AUTH_KEY',
		'SECURE_AUTH_KEY',
		'LOGGED_IN_KEY',
		'NONCE_KEY',
		'AUTH_SALT',
		'SECURE_AUTH_SALT',
		'LOGGED_IN_SALT',
		'NONCE_SALT',
	))->notEmpty();
}

/**
 * Disallow on server file edits
 */
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );

/*
 * Define site URL
 */
define( 'WP_HOME', getenv( 'WP_HOME' ) !== false ? getenv( 'WP_HOME' ) : 'https://backend.hazelsheritage.com/' );
define( 'WP_SITEURL', getenv( 'WP_SITEURL' ) !== false ? getenv( 'WP_SITEURL' ) : 'https://backend.hazelsheritage.com/wp/' );

/**
 * Set Database Details
 */
define( 'DB_NAME', getenv( 'DB_NAME' ) );
define( 'DB_USER', getenv( 'DB_USER' ) );
define( 'DB_PASSWORD', getenv( 'DB_PASSWORD' ) !== false ? getenv( 'DB_PASSWORD' ) : '' );
define( 'DB_HOST', getenv( 'DB_HOST' ) );

/**
 * Set debug modes
 */
define( 'WP_DEBUG', getenv( 'WP_DEBUG' ) === 'true' ? true : false );
define( 'IS_LOCAL', getenv( 'IS_LOCAL' ) !== false ? true : false );

/**
 * Force SSL
 */
define('FORCE_SSL_ADMIN', !IS_LOCAL);

/**
 * Limit post revisions
 */
define( 'WP_POST_REVISIONS', 3 );

/*
 * Define wp-content directory outside of WordPress directory
 */
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/wp-content' );
define( 'WP_CONTENT_URL', getenv( 'WP_CONTENT_URL' ) !== false ? getenv( 'WP_CONTENT_URL' ) : 'https://backend.hazelsheritage.com/wp-content' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', getenv( 'AUTH_KEY' ) );
define( 'SECURE_AUTH_KEY', getenv( 'SECURE_AUTH_KEY' ) );
define( 'LOGGED_IN_KEY', getenv( 'LOGGED_IN_KEY' ) );
define( 'NONCE_KEY', getenv( 'NONCE_KEY' ) );
define( 'AUTH_SALT', getenv( 'AUTH_SALT' ) );
define( 'SECURE_AUTH_SALT', getenv( 'SECURE_AUTH_SALT' ) );
define( 'LOGGED_IN_SALT', getenv( 'LOGGED_IN_SALT' ) );
define( 'NONCE_SALT', getenv( 'NONCE_SALT' ) );
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = getenv( 'DB_PREFIX' ) !== false ? getenv( 'DB_PREFIX' ) : 'wp_';

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
