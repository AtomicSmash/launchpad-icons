# AS compiler helpers

A group of PHP helpers for the new AS compiler. For use with the npm package `@atomicsmash/compiler`.

## Usage

This package currently only does one thing. It contains a PHP class for loading assets using the asset manifest and wordpress dependency info. This takes out a bunch of the hard work of interpreting the manifests and makes it easier to load files by the source names.

### Overview

First, create your own local Assets class by extending the one in this package:

```php
class Assets extends \AtomicSmash\CompilerHelpers\Assets {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->context_folder = realpath( __DIR__ . '/../' ); // Required.
		$this->build_folder = '/build/'; // Optional. Defaults to `/dist/`.
		parent::__construct();
	}
}
```

Then use that class to enqueue your assets like this:

```php
function scripts() {
  $assets = new Assets();

  // Styles
  $example_stylesheet = $assets->get_cached_asset( 'styles/styles.scss' );
  if ( $example_stylesheet === null ) {
    throw new Error( "Failed to load stylesheet styles/styles.scss" );
  }
  wp_enqueue_style( 'handle', $example_stylesheet['source'], $example_stylesheet['dependencies'], $example_stylesheet['version'] );

  // Scripts
  $example_script = $assets->get_cached_asset( 'scripts/example.ts' );
  if ( $example_script === null ) {
    throw new Error( "Failed to load script scripts/example.ts" );
  }
  wp_enqueue_script( 'handle', $example_script['source'], $example_script['dependencies'], $example_script['version'], array( 'strategy' => 'defer' ) );
}
add_action( 'wp_enqueue_scripts', 'scripts', 10 );
```

### `get_cached_asset($source): $output`

Params:

- `$source` - The source file name relative to the src folder.

Returns:

- `$output` - An array of information about the compiled file, or NULL if the file is not found.
- `$output['source']` - The public URL of the compiled file.
- `$output['dependencies']` - An array of dependencies for any WP packages e.g. array('wp-dom-ready')
- `$output['version']` - The output file fingerprint of the compiled file.

## Development

### To release a new version:

1. Make any changes you need to the package
2. Merge into main
3. An automated action will generate a PR called Version Packages. Review it and then merge this in.
4. An action will tag the repo which will in turn cause packagist to release a new version.
