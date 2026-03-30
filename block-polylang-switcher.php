<?php
/**
 * Plugin Name: Block Polylang Switcher
 * Description: Gutenberg block that renders the Polylang language switcher. Requires Polylang.
 * Version: 0.2.0
 * Author: Werk3D
 * Requires at least: 6.0
 * Requires Plugins: polylang
 *
 * @package Block_Polylang_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BPS_VERSION', '0.2.0' );
define( 'BPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once BPS_PLUGIN_DIR . 'includes/functions.php';

