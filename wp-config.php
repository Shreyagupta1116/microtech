<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'microsoltion' );
/** MySQL database username */
define( 'DB_USER', 'microsoltion' );
/** MySQL database password */
define( 'DB_PASSWORD', 'microsoltion' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'zkxFK&,kbs:FM9b#C!^hX<f$_ldh^D4.7K+LeTayD$NVbMmnS;eFUWz?m)p+^d;u' );
define( 'SECURE_AUTH_KEY',  '.gP4td^wQeeftSEwVkj/J$)1!]hpW~^yMctY+qD$mrEN:R4AG@;Yz7(f^ev{}ATE' );
define( 'LOGGED_IN_KEY',    'Y6`Ny|FK1VRHUQixK=#2,~r3^(Sm[X?xHdj[Z52A7m9by<z1Y!$W~3+9Z>rZi[m(' );
define( 'NONCE_KEY',        ']~^-TQ:%DagY<EyJ40FaZJu5@P7xXCt=u [O)ENluXnd`{Jd2`:/+[k> cA.qWd>' );
define( 'AUTH_SALT',        '^>zuz0f3mYP(-qwyFZ!He(6yz[BR29ofwe~<m[HT{Hk1#R$1 TJEp5LTWjNXLxFO' );
define( 'SECURE_AUTH_SALT', 'V*J/}q6_<4p=Za*wWxubT5p}mSTEAa]05R;^^ZudV1$+LYDE$GbqzfV+({W9CRja' );
define( 'LOGGED_IN_SALT',   'N7{ho=qd3[Tf.XbPP^sTdLe{==00 I<8L$h#0NM:1>)seIh ^x&J{EHCeb8TP&?:' );
define( 'NONCE_SALT',       'PT_GdXY:1/^G3qmV9Ea}$c0MaT<L;D/k7SBxGR)@9dS,Q;-yg<HQEzmkTJS)Q&,;' );
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'micr_';
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
