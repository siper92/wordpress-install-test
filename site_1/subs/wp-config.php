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
define( 'AUTH_KEY',         'm^If<^w#;ES!%tE~)XUf|GDc<mgr/6w+m8mFa==8g~],)Hv+BQi,G[ihc!FH3<W%' );
define( 'SECURE_AUTH_KEY',  'VSGhpj*~4bTu$t}CZ0I5{*B!?2kX*_RMevAcYL`t [vC fxi>W_XB3%I!~8%rCdb' );
define( 'LOGGED_IN_KEY',    'zHsl{T3}4d2w1Hjm+:L-,SB<-?7UmP&a<HXPQ>5.ZiQV,XsN9)Lwwr^hkwBoj@Po' );
define( 'NONCE_KEY',        'ZWdm^|jm,#j/tentv*q{R | O,dRC-[by#)vk%R|Fo7a;rzx}<tB:2f*xaAX5t/}' );
define( 'AUTH_SALT',        '=shN% BclBD~nJ7m}R:D/&BHVmlz<^me_U$-VD?I Qh&A 6++1@g`oTnG2Ryx@#V' );
define( 'SECURE_AUTH_SALT', '`#8b8.JmT8ZT}m2^xXM56FR{2>v*GLY`Y~L@exbRGJ(h3?x~^OfBBo!@8bQCiA]2' );
define( 'LOGGED_IN_SALT',   'j5N,`7@ydf^VjxOi7VG%*!lKPX74f=c~;Pr%w*t9B`Jkl@SU2q-$|*x=4D*MmMC]' );
define( 'NONCE_SALT',       'Rv@0T%ZAT8(,pa]B`9j3*G+QFk TE5[/812N)B5kB?RV_#S3`LP;~_.T8RfqG0}j' );

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
$table_prefix = 'wp_subs_';

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



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
