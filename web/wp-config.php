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
 * Disallow on server file edits
 */
if( !IS_LOCAL ){
	define( 'DISALLOW_FILE_EDIT', true );
	define( 'DISALLOW_FILE_MODS', true );
}

/**
 * Limit post revisions
 */
define( 'WP_POST_REVISIONS', 3 );

/**
 * Non Pantheon config
 */
if ( !isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ):

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

endif;

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = getenv( 'DB_PREFIX' ) !== false ? getenv( 'DB_PREFIX' ) : 'wp_';

/**
 * Begin Pantheon wp-config.php settings
 *
 *         .+?:
 *          .+??.
 *            ??? .
 *            +???.
 *       +?????????=.
 *       .???????????.
 *       .????????????.
 *
 *      ########### ########
 *      ############.#######.
 *      ####### ####  .......
 *      ######## #### #######
 *      #########.####.######
 *      ######  ...
 *      #######.??.##########
 *      #######~+??.#########
 *      ########.??..
 *      #########.??.#######.
 *      #########.+?? ######.
 *                .+?.
 *          .????????????.
 *            +??????????,
 *             .????++++++.
 *               ????.
 *               .???,
 *                .~??.
 *                  .??
 *                   .?,.
 */

if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ):
	// ** MySQL settings - included in the Pantheon Environment ** //
	/** The name of the database for WordPress */
	define( 'DB_NAME', $_ENV['DB_NAME'] );

	/** MySQL database username */
	define( 'DB_USER', $_ENV['DB_USER'] );

	/** MySQL database password */
	define( 'DB_PASSWORD', $_ENV['DB_PASSWORD'] );

	/** MySQL hostname; on Pantheon this includes a specific port number. */
	define( 'DB_HOST', $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'] );

	/** Database Charset to use in creating database tables. */
	define( 'DB_CHARSET', 'utf8' );

	/** The Database Collate type. Don't change this if in doubt. */
	define( 'DB_COLLATE', '' );

	/**#@+
	 * Authentication Unique Keys and Salts.
	 *
	 * Change these to different unique phrases!
	 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
	 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
	 *
	 * Pantheon sets these values for you also. If you want to shuffle them you
	 * can do so via your dashboard.
	 *
	 * @since 2.6.0
	 */
	define( 'AUTH_KEY', $_ENV['AUTH_KEY'] );
	define( 'SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY'] );
	define( 'LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY'] );
	define( 'NONCE_KEY', $_ENV['NONCE_KEY'] );
	define( 'AUTH_SALT', $_ENV['AUTH_SALT'] );
	define( 'SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT'] );
	define( 'LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT'] );
	define( 'NONCE_SALT', $_ENV['NONCE_SALT'] );
	/**#@-*/

	/** A couple extra tweaks to help things run well on Pantheon. **/
	if ( isset( $_SERVER['HTTP_HOST'] ) ) {
		// HTTP is still the default scheme for now.
		$scheme = 'http';
		// If we have detected that the end use is HTTPS, make sure we pass that
		// through here, so <img> tags and the like don't generate mixed-mode
		// content warnings.
		if ( isset( $_SERVER['HTTP_USER_AGENT_HTTPS'] ) && $_SERVER['HTTP_USER_AGENT_HTTPS'] == 'ON' ) {
			$scheme = 'https';
		}
		define( 'WP_HOME', $scheme . '://' . $_SERVER['HTTP_HOST'] );
		define( 'WP_SITEURL', $scheme . '://' . $_SERVER['HTTP_HOST'] . '/wp' );

		/*
		 * Define wp-content directory outside of WordPress directory
		 */
		define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/wp-content' );
		define( 'WP_CONTENT_URL', $scheme . '://' . $_SERVER['HTTP_HOST'] . '/wp-content' );
	}

	// Force the use of a safe temp directory when in a container
	if ( defined( 'PANTHEON_BINDING' ) ):
		define( 'WP_TEMP_DIR', sprintf( '/srv/bindings/%s/tmp', PANTHEON_BINDING ) );
	endif;
endif;

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
