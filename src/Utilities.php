<?php
/**
 * Utility functions for general use
 *
 * @package BigWing\PetAndGo
 */

namespace BigWing\PetAndGo;

/**
 * Trait Utilities
 *
 * @package BigWing\PetAndGo
 */
trait Utilities {

	/**
	 * Converts a CamelCase string to snake_case.
	 *
	 * @link https://stackoverflow.com/a/35719689/1588534
	 *
	 * @param string $string The string to convert.
	 * @return string The converted string, or the original if there's an error converting.
	 */
	public function decamelize( string $string ): string {
		$snake = preg_replace(
			[ '/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/' ],
			'$1_$2',
			$string
		);

		if ( is_null( $snake ) ) {
			( new PGLogger() )->error(
				sprintf( 'Error converting %s to snake_case', $string ),
				array(
					'function' => __METHOD__,
					'string'   => $string,
				)
			);

			return $string;
		}

		return strtolower( $snake );
	}

	/**
	 * Retrieve a value from transients. If it doesn't exist, run the $callback to generate and
	 * cache the value.
	 *
	 * @link https://github.com/stevegrunwell/wp-cache-remember
	 *
	 * @param string   $key      The transient key.
	 * @param callable $callback The callback used to generate and cache the value.
	 * @param int      $expire   Optional. The number of seconds before the cache entry should expire.
	 *                           Default is 0 (as long as possible).
	 * @return mixed The value returned from $callback, pulled from transients when available.
	 */
	public function remember_transient( $key, $callback, $expire = 0 ) {
		$cached = get_transient( $key );

		if ( false !== $cached ) {
			return $cached;
		}

		$value = $callback();

		if ( ! is_wp_error( $value ) ) {
			set_transient( $key, $value, $expire );
		}

		return $value;
	}

	public function get_xpath_from_endpoint( string $endpoint ): string {
		return '//' . lcfirst( $endpoint );
	}
}
