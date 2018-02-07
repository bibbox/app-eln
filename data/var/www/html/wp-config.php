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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'elndemo');

/** MySQL database username */
define('DB_USER', 'elndemo');

/** MySQL database password */
define('DB_PASSWORD', 'Dem04eln!');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'cMp:?R}<A3HL5sZGp &}-8|!</sF4$7Y;J|&`k<{R6pv$I,:SwMyvtVXu/t^G`6]');
define('SECURE_AUTH_KEY',  '4Xb8?]O#B0!unN+=Z&CosWj#bVctM3b.!DvqUG/2}1k/Cqi?,kL*wKpBr{2*aI^c');
define('LOGGED_IN_KEY',    '^po(^/7|M2*u)$4ts}=58)@8/ ij@eEi&/A2lefY2VW(84eA,:&8v<zpvj0Z)T!5');
define('NONCE_KEY',        'u)iU-jm@^PxbqpLAgQz1zK,|X,uaB4tt5]N.-^@,*.1QZK3V;TU`c|?Rwt!50/dh');
define('AUTH_SALT',        '_#N~;/EAXkp^e6QzU $c3o1%6/^n75AZM[`Z`ZX/xvQ[|R7`QSHn(4T@<uN(?1S5');
define('SECURE_AUTH_SALT', ')k,f3Q=>[7`[jS|TigesC-4:p}8Iq9s$L+)[uM Dx1:BSm#Pu!Ex-T.;l:P+HYf$');
define('LOGGED_IN_SALT',   '>1R:kpp{sQ5saXZr(`X|j9B6<ARuO!0ck)PG7eFry~rCbPyqd1QnsCIk#KnzNPy%');
define('NONCE_SALT',       'nW1A{S?K!avphy[u<_ek!GGhussP6^b-}eRd|B.359rg6+UJZB~(8@cc<3a`+4nj');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
