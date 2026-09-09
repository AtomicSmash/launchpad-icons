<?php
/**
 * Plugin Name:       Launchpad icons
 * Description:       This adds the Launchpad icon set to Launchpad Blocks.
 * Requires at least: 6.9
 * Requires PHP:      8.2
 * Version:           0.1.0
 * Author:            Atomic Smash
 * Author URI:        https://www.atomicsmash.co.uk/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       launchpad-icons
 * Requires Plugins:  launchpad-blocks
 */

namespace Launchpad\Icons;

define( 'LAUNCHPAD_ICONS_VERSION', '0.1.0' );

// Require autoloader.
require __DIR__ . '/vendor/autoload.php';

/**
 * Icon function
 *
 * This function generates an SVG from an icon name and allows custom attributes to be passed in
 *
 * @param string                    $icon_name Icon name.
 * @param array<string,string|bool> $attributes The HTML attributes to add to the SVG element.
 */
function icon( string $icon_name, array $attributes = array() ): string {
	$assets = new \Launchpad\Icons\Assets();
	$icon_sprite = $assets->get_cached_asset( 'icons/sprite.svg' );
	$attrs = join(
		' ',
		array_map(
			function ( $key ) use ( $attributes ) {
				if ( is_bool( $attributes[ $key ] ) ) {
					return $attributes[ $key ] ? $key : '';
				}
				return $key . '="' . $attributes[ $key ] . '"';
			},
			array_keys( $attributes )
		)
	);

	$result = '<svg xmlns="http://www.w3.org/2000/svg" ' . $attrs . '><use href="' . $icon_sprite['source'] . '#' . $icon_name . '"></use></svg>';
	return $result;
}

/**
 * Add Launchpad icon renderer to icon block.
 *
 * @param array<string,function> $renderers An associative array of the currently registered renderers.
 *
 * @return array<string,function>
 */
function add_launchpad_icons_renderer_to_icon_block( array $renderers ): array {
	return array(
		'launchpad' => function ( string $icon_name, array $attributes = array() ) {
			return \Launchpad\Icons\icon( $icon_name, $attributes );
		},
		...$renderers,
	);
}
add_filter( 'launchpad_blocks_icon_renderers', __NAMESPACE__ . '\\add_launchpad_icons_renderer_to_icon_block' );

require_once 'functions/enqueue-assets.php';
