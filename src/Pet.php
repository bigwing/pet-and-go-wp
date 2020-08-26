<?php

namespace BigWing\PetAndGo;

/**
 * Class Pet
 *
 * @package BigWing\PetAndGo
 */
class Pet {

	/**
	 * @return $this
	 */
	public function __construction() {
		return $this;
	}

	/**
	 * Get the currently set variables for the class.
	 * @return string[]
	 */
	public function get_fields(): array {
		$fields = array();
		foreach ( get_object_vars( $this ) as $key => $val ) {
			$fields[] = $key;
		}

		return $fields;
	}

	public function get_field( string $field ) {
		return $this->$field;
	}

	/**
	 * @param string|true $data
	 */
	public function set_field( string $field, $data ) {
		$this->$field = $data;
	}

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

	public function is_featured(): bool {
		return 'No' !== $this->get_field( 'Featured' );
	}

}
