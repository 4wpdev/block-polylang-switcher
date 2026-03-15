<?php
/**
 * Plugin Name: Block Polylang Switcher
 * Description: Gutenberg block that renders the Polylang language switcher. Requires Polylang.
 * Version: 1.0.0
 * Author: Werk3D
 * Requires at least: 6.0
 * Requires Plugins: polylang
 *
 * @package Block_Polylang_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BPS_VERSION', '1.0.0' );
define( 'BPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Register the Polylang switcher block (реєстрація через PHP, без залежності від block.json).
 */
function bps_register_block() {
	register_block_type(
		'bps/polylang-switcher',
		array(
			'api_version'      => 2,
			'title'            => _x( 'Polylang Language Switcher', 'block title', 'block-polylang-switcher' ),
			'category'         => 'widgets',
			'description'      => _x( 'Displays the Polylang language switcher. Requires Polylang plugin.', 'block description', 'block-polylang-switcher' ),
			'keywords'         => array( 'polylang', 'language', 'switcher', 'translation', 'lang' ),
			'textdomain'       => 'block-polylang-switcher',
			'render_callback'  => 'bps_render_switcher',
			'attributes'       => array(),
			'style'            => 'bps-polylang-switcher',
			'script'           => 'bps-polylang-switcher',
			'supports'         => array(
				'html'       => false,
				'align'      => array( 'left', 'right', 'center' ),
				'className'  => true,
			),
		)
	);
}

/**
 * Register block assets (style + script) so they load when block is used.
 */
function bps_register_assets() {
	wp_register_style(
		'bps-polylang-switcher',
		plugins_url( 'style.css', __FILE__ ),
		array(),
		BPS_VERSION
	);
	wp_register_script(
		'bps-polylang-switcher',
		plugins_url( 'front.js', __FILE__ ),
		array(),
		BPS_VERSION,
		true
	);
}

add_action( 'init', 'bps_register_assets', 5 );
add_action( 'init', 'bps_register_block', 20 );

/**
 * Enqueue editor script so the block appears in the inserter (JS registration).
 */
function bps_enqueue_editor_assets() {
	$asset = plugins_url( 'editor.js', __FILE__ );
	wp_enqueue_script(
		'bps-polylang-switcher-editor',
		$asset,
		array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor' ),
		BPS_VERSION
	);
}

add_action( 'enqueue_block_editor_assets', 'bps_enqueue_editor_assets' );

/**
 * Render the language switcher as a styled select (EN / DE). Current language selected by default; no duplicate.
 *
 * @param array $attributes Block attributes (reserved for future options).
 * @return string HTML output.
 */
function bps_render_switcher( $attributes = array() ) {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return '';
	}

	$languages = pll_the_languages(
		array(
			'raw'  => 1,
			'echo' => 0,
		)
	);

	if ( empty( $languages ) || ! is_array( $languages ) ) {
		return '';
	}

	$classes = array( 'bps-switcher', 'bps-switcher--select', 'wp-block-bps-polylang-switcher' );
	$class   = implode( ' ', $classes );

	$html = '<nav class="' . esc_attr( $class ) . '" aria-label="' . esc_attr__( 'Language switcher', 'block-polylang-switcher' ) . '">';
	$html .= '<select class="bps-switcher__select" aria-label="' . esc_attr__( 'Select language', 'block-polylang-switcher' ) . '" data-bps-switcher>';

	foreach ( $languages as $lang ) {
		$slug    = isset( $lang['slug'] ) ? $lang['slug'] : '';
		$url     = isset( $lang['url'] ) ? $lang['url'] : '';
		$label   = strtoupper( $slug );
		$selected = ! empty( $lang['current_lang'] ) ? ' selected' : '';
		$html .= '<option value="' . esc_url( $url ) . '"' . $selected . '>' . esc_html( $label ) . '</option>';
	}

	$html .= '</select></nav>';

	return $html;
}

