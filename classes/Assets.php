<?php
/**
 * Assets class
 */

namespace Launchpad\Icons;

/**
 * Get assets from launchpad blocks plugin.
 */
class Assets extends \AtomicSmash\CompilerHelpers\Assets {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->context_folder = realpath( __DIR__ . '/../' );
		parent::__construct();
	}
}
