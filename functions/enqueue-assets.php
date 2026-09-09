<?php
/**
 * Enqueue global assets
 */

namespace Launchpad\Icons\Enqueue;

use Error;

/**
 * Enqueue scripts in the editor.
 *
 * @throws Error If there's an issue loading the asset.
 */
function editor_scripts(): void {
	$assets = new \Launchpad\Icons\Assets();
	$load_icons_script = $assets->get_cached_asset( 'scripts/load-icons-in-editor.tsx' );
	if ( null === $load_icons_script ) {
		throw new Error( 'Failed to load script: scripts/load-icons-in-editor.tsx' );
	}
	wp_enqueue_script(
		'launchpad-icons-load-icons',
		$load_icons_script['source'],
		$load_icons_script['dependencies'],
		$load_icons_script['version'],
		array( 'strategy' => 'defer' )
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\editor_scripts', 10 );
