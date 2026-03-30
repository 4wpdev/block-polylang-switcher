<?php
/**
 * Core block registration and rendering for Block Polylang Switcher.
 *
 * @package Block_Polylang_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Asset URL version query arg (?ver=): uses the file mtime so caches bust after CSS/JS changes.
 * Falls back to BPS_VERSION. To use only semver: add_filter( 'bps_asset_version_use_filemtime', '__return_false' );
 *
 * @param string $relative_path Path relative to the plugin root, e.g. assets/css/style.css.
 */
function bps_get_asset_version( string $relative_path ): string {
	if ( ! apply_filters( 'bps_asset_version_use_filemtime', true, $relative_path ) ) {
		return BPS_VERSION;
	}
	$path = BPS_PLUGIN_DIR . ltrim( $relative_path, '/' );
	if ( is_readable( $path ) ) {
		$mtime = filemtime( $path );
		if ( false !== $mtime ) {
			return (string) $mtime;
		}
	}
	return BPS_VERSION;
}

/**
 * Registers the Polylang switcher block in PHP (no block.json dependency).
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
			'attributes'       => array(
				'view' => array(
					'type'    => 'string',
					'default' => 'dropdown', // dropdown | list.
				),
			),
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
 * Відносний шлях до front CSS: style.min.css якщо є і не SCRIPT_DEBUG, інакше style.css.
 */
function bps_get_frontend_style_rel(): string {
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		return 'assets/css/style.css';
	}
	if ( is_readable( BPS_PLUGIN_DIR . 'assets/css/style.min.css' ) ) {
		return 'assets/css/style.min.css';
	}
	return 'assets/css/style.css';
}

/**
 * Register block assets (style + script) so they load when block is used.
 */
function bps_register_assets() {
	$style_rel = bps_get_frontend_style_rel();
	wp_register_style(
		'bps-polylang-switcher',
		BPS_PLUGIN_URL . $style_rel,
		array(),
		bps_get_asset_version( $style_rel )
	);
	wp_register_script(
		'bps-polylang-switcher',
		BPS_PLUGIN_URL . 'assets/js/front.js',
		array(),
		bps_get_asset_version( 'assets/js/front.js' ),
		true
	);
}

add_action( 'init', 'bps_register_assets', 5 );
add_action( 'init', 'bps_register_block', 20 );

/**
 * Enqueue editor script so the block appears in the inserter (JS registration).
 */
function bps_enqueue_editor_assets() {
	$asset = BPS_PLUGIN_URL . 'assets/js/editor.js';
	wp_enqueue_script(
		'bps-polylang-switcher-editor',
		$asset,
		array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor' ),
		bps_get_asset_version( 'assets/js/editor.js' )
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

	$view    = isset( $attributes['view'] ) && 'list' === $attributes['view'] ? 'list' : 'dropdown';
	$classes = array( 'bps-switcher', 'wp-block-bps-polylang-switcher' );
	if ( 'dropdown' === $view ) {
		$classes[] = 'bps-switcher--dropdown';
	} else {
		$classes[] = 'bps-switcher--list';
	}
	$class   = implode( ' ', $classes );

	$current = null;
	$items   = array();

	foreach ( $languages as $lang ) {
		$slug  = isset( $lang['slug'] ) ? $lang['slug'] : '';
		$url   = isset( $lang['url'] ) ? $lang['url'] : '';
		$label = strtoupper( $slug );

		if ( ! empty( $lang['current_lang'] ) ) {
			$current = array(
				'slug'  => $slug,
				'url'   => $url,
				'label' => $label,
			);
		} else {
			$items[] = array(
				'slug'  => $slug,
				'url'   => $url,
				'label' => $label,
			);
		}
	}

	if ( ! $current && ! empty( $items ) ) {
		$current = array_shift( $items );
	}

	if ( ! $current ) {
		return '';
	}

	if ( 'list' === $view ) {
		$html  = '<nav class="' . esc_attr( $class ) . '" aria-label="' . esc_attr__( 'Language switcher', 'block-polylang-switcher' ) . '">';
		$html .= '<ul class="bps-switcher__list-inline">';

		// Current language first.
		$html .= '<li class="bps-switcher__item bps-switcher__item--current"><span class="bps-switcher__current" data-bps-current-lang="' . esc_attr( $current['slug'] ) . '">' . esc_html( $current['label'] ) . '</span></li>';

		// Other languages as links.
		foreach ( $items as $item ) {
			$html .= '<li class="bps-switcher__item">';
			$html .= '<a class="bps-switcher__link" href="' . esc_url( $item['url'] ) . '" data-bps-lang="' . esc_attr( $item['slug'] ) . '">';
			$html .= esc_html( $item['label'] );
			$html .= '</a>';
			$html .= '</li>';
		}

		$html .= '</ul></nav>';
		return $html;
	}

	// Default: dropdown view.
	$html  = '<nav class="' . esc_attr( $class ) . '" aria-label="' . esc_attr__( 'Language switcher', 'block-polylang-switcher' ) . '" data-bps-switcher-root>';
	$html .= '<button type="button" class="bps-switcher__toggle" aria-haspopup="true" aria-expanded="false">';
	$html .= '<span class="bps-switcher__current" data-bps-current-lang="' . esc_attr( $current['slug'] ) . '">' . esc_html( $current['label'] ) . '</span>';
	$html .= '<span class="bps-switcher__icon" aria-hidden="true"></span>';
	$html .= '</button>';

	if ( ! empty( $items ) ) {
		$html .= '<ul class="bps-switcher__list" hidden>';
		foreach ( $items as $item ) {
			$html .= '<li class="bps-switcher__item">';
			$html .= '<button type="button" class="bps-switcher__option" data-bps-lang="' . esc_attr( $item['slug'] ) . '" data-bps-url="' . esc_url( $item['url'] ) . '">';
			$html .= esc_html( $item['label'] );
			$html .= '</button>';
			$html .= '</li>';
		}
		$html .= '</ul>';
	}

	$html .= '</nav>';

	return $html;
}

