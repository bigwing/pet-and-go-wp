<?php

namespace BigWing\PetAndGo;

/**
 * Class Pet
 *
 * @package BigWing\PetAndGo
 */
class Pet {

	use Utilities;

	/**
	 * Store for API data.
	 *
	 * @var array $data
	 */
	private $data = array();

	/**
	 * @return $this
	 */
	public function __construct() {
		return $this;
	}

	/**
	 * @see Pet::get_field()
	 *
	 * @param string $name The API field name.
	 * @return mixed|string The field value; empty string if field isn't set.
	 */
	public function __get( string $name ) {
		return $this->get_field( $name );
	}

	/**
	 * @see Pet::set_field()
	 *
	 * @param string $name  The field name.
	 * @param mixed  $value The field value.
	 */
	public function __set( string $name, $value ) {
		$this->set_field( $name, $value );
	}

	/**
	 * Removes a field from the data store.
	 *
	 * @param string $name The API field name.
	 */
	public function __unset( string $name ) {
		$this->data = array_diff_key( $this->data, [ $name => $this->data[ $name ] ] );
	}

	/**
	 * Get the currently set variables for the class.
	 *
	 * @return string[]
	 */
	public function get_field_names(): array {
		return array_keys( $this->data );
	}

	/**
	 * Gets the details for this pet.
	 *
	 * @param string $key_case Optional. Defines the type of array key used.
	 *                         'snake'|'snake_case' converts the API XML field names to WP-style lowercase snake_case.
	 *                         'camel'|'CamelCase' retains the original API XML field names.
	 *                         Default 'camel'.
	 * @return array A keyed array of data for this pet, keyed to $key_case format.
	 */
	public function get_pet_details_array( string $key_case = 'camel' ): array {
		$details   = $this->data;
		$key_case  = strtolower( $key_case );
		$key_cases = array( 'snake', 'snake_case', 'camel', 'CamelCase' );

		if ( empty( $key_case ) || ! in_array( $key_case, $key_cases, true ) ) {
			$key_case = 'camel';
		}

		if ( 'snake' === $key_case || 'snake_case' === $key_case ) {
			$snake = array();
			foreach ( $details as $key => $value ) {
				$snake[ $this->decamelize( $key ) ] = $value;
				$this->__unset( $key );
			}

			$details = $snake;
		}

		return $details;
	}

	/**
	 * Gets the data for a given API field name.
	 *
	 * @param string $field The field name.
	 * @return mixed|string The field data; empty string if field not set.
	 */
	public function get_field( string $field ) {
		if ( array_key_exists( $field, $this->data ) ) {
			return $this->data[ $field ];
		}

		return '';
	}

	/**
	 * Sets the data store for a given field.
	 *
	 * @param string $field The field name.
	 * @param mixed  $data  The field data.
	 */
	public function set_field( string $field, $data ) {
		$this->data[ $field ] = $data;
	}

	/**
	 * Checks the Location field text to determine if a pet is available for adoption today.
	 *
	 * @return bool True if they are at the adoption center or an event; false if they are not.
	 */
	public function is_adoptable_today(): bool {
		$location = $this->get_field( 'Location' );

		if ( empty( $location ) ) {
			return false;
		}

		$featured_strings = array(
			'i am at the adoption center today!',
			"i'm at an event today!",
			'i am at an event today!',
		);

		if ( ! in_array( strtolower( $location ), $featured_strings, true ) ) {
			return false;
		}

		$this->set_field( 'is_adoptable_today', true );

		return true;
	}

	/**
	 * Checks the API to see if they are a featured pet today.
	 *
	 * @return bool True if the featured flag is set in the API; false if not.
	 */
	public function is_featured(): bool {
		return (bool) stripos( $this->get_field( 'Featured' ), 'y' );
	}
}
