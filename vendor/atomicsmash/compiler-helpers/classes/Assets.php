<?php
/**
 * Assets class
 */

namespace AtomicSmash\CompilerHelpers;

use Error;

/**
 * The base class for getting assets from the new compiler.
 */
abstract class Assets {
	/**
	 * Context folder e.g. the theme or plugin root folder. Prefer a fixed folder path over get_stylesheet_directory().
	 *
	 * @var string|null
	 */
	protected $context_folder = null;

	/**
	 * The folder to find the built assets.
	 *
	 * @var string
	 */
	protected $build_folder = '/dist/';

	/**
	 * Manifest content
	 *
	 * @var array<string,string>
	 */
	public $manifest;

	/**
	 * WP asset info file.
	 *
	 * @var array<string,array{dependencies:string[],version:string}>
	 */
	public $asset_info;

	/**
	 * Constructor
	 *
	 * @throws Error If trying to use the class directly.
	 */
	public function __construct() {
		if ( null === $this->context_folder ) {
			throw new Error( 'Don\'t use the assets class directly. Instead, extend it and add the context folder parameter.' );
		}
		if ( ! str_ends_with( $this->build_folder, '/' ) ) {
			$this->build_folder = $this->build_folder . '/';
		}
		if ( ! str_ends_with( $this->context_folder, '/' ) ) {
			if ( str_starts_with( $this->build_folder, '/' ) ) {
				$this->build_folder = substr( $this->build_folder, 1 );
			}
			$this->context_folder = $this->context_folder . '/';
		}
		$this->manifest = $this->get_manifest();
		$this->asset_info = $this->get_asset_info();
	}

	/**
	 * Get the asset manifest
	 *
	 * @return array<string,string>
	 */
	private function get_manifest(): array {
		$manifest_locations = array(
			'assets-manifest.json',
			'mix-manifest.json', // Legacy
		);
		foreach ( $manifest_locations as $manifest_location ) {
			if ( file_exists( $this->context_folder . $this->build_folder . $manifest_location ) ) {
				return wp_json_file_decode(
					$this->context_folder . $this->build_folder . $manifest_location,
					array(
						'associative' => true,
					)
				);
			}
		}
		return array();
	}

	/**
	 * Get the WordPress asset info
	 *
	 * @return array<string,array{dependencies:string[],version:string}>
	 */
	private function get_asset_info(): array {
		$asset_info_locations = array(
			'wordpress-assets-info.php',
			'assets.php', // Legacy
		);
		foreach ( $asset_info_locations as $asset_info_location ) {
			if ( file_exists( $this->context_folder . $this->build_folder . $asset_info_location ) ) {
				return require $this->context_folder . $this->build_folder . $asset_info_location;
			}
		}
		return array();
	}

	/**
	 * Get cached asset
	 *
	 * Gets the file info of a cached asset from the manifest file. This function will search for an entry in the child theme manifest first, falling back to the manifest in the parent theme, and returning an error if it's not found in either.
	 *
	 * @param string $filename Name of the file in the manifest.
	 * @return array{source:string,dependencies:string[],version:string|null}|null A collection of info on the asset, including source, dependencies and version, or null if the asset can't be found.
	 */
	public function get_cached_asset( string $filename ): array|null {
		$asset = $this->manifest[ $filename ] ?? null;
		if ( null === $asset ) {
			return null;
		}
		return array(
			'source' => home_url( substr( $this->context_folder, strpos( $this->context_folder, 'wp-content' ) ) . $this->build_folder . $asset ),
			'dependencies' => isset( $this->asset_info[ $asset ] ) ? $this->asset_info[ $asset ]['dependencies'] : array(),
			'version' => isset( $this->asset_info[ $asset ] ) ? $this->asset_info[ $asset ]['version'] : null,
		);
	}
}
