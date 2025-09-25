<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_app' );

/** Database username */
define( 'DB_USER', 'user' );

/** Database password */
define( 'DB_PASSWORD', 'userPass' );

/** Database hostname */
define( 'DB_HOST', 'db' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '^NLb*4?N(9b)::E3vejSl^t`g343FXf~ed4@)dLWB!eL~^W!hWg{4>@9Sa4*n}ga' );
define( 'SECURE_AUTH_KEY',  'C#3Tea`&XDKJ6;$NsEyb)uBj+__TV{70(a;8Y7zc_dTW^&qDY7Ct_=?{Pq^d^-GX' );
define( 'LOGGED_IN_KEY',    '77`7`PoAb-Jnzbx*0P{n[3e2?_=cM4,B6:wA/Cx0K9:?jQkfM&+>%X;#(:muTnDB' );
define( 'NONCE_KEY',        'YAg/&5wP%g),b.y/x;S7$GJ<]d!:>iD-Op >lxys{OXwXN67 Xl;EOsp6b,Q}nCP' );
define( 'AUTH_SALT',        'QK$nfvD(QU`8-LmG<lD}`AYulFq`o8 FI&R!Jus<w yo*$Tk6e-M g1<43_WM$hW' );
define( 'SECURE_AUTH_SALT', 'mGFhYwMG@CNNGDSRc5-P]UbYW_j}Y @Vi3AY&6Hi#n4zJQ!fp5Q]ELUlIZOX.eD/' );
define( 'LOGGED_IN_SALT',   'XDcFU(6Y`|v@Ij8`qOG#|7tq/9Y)4]dr^V<0Ddo3(#hrzkEpzwAK`-h.N.XkNHe4' );
define( 'NONCE_SALT',       'ucK=*zpet:<Bjn(cUw*V`HoG;UO#hv^j,%0>|RcI0G}ox8fyz`ZC7e?WJe0V$h?K' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

/* Multisite */
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', 'localhost' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
