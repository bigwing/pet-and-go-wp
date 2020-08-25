<?php
/**
 * Parser for API responses into usable data
 *
 * @package BigWing\PetAndGo
 */

namespace BigWing\PetAndGo;

use SimpleXMLElement;
use WP_Error;

/**
 * Class PGParser
 *
 * @package BigWing\PetAndGo
 */
class PGParser {
	use Utilities;

	/**
	 * Converts a pet class to aN array.
	 *
	 * @param \BigWing\PetAndGo\Pet $pet    The Pet to convert.
	 * @param string                $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 *                                      ARRAY_N returns an array of arrays indexed from 0 in the format of
	 *                                      (('field' => field, 'value' => value), ...).
	 *                                      ARRAY_A returns an associative array (key => value, ...),
	 *                                      with the key set to a snake_case version of the XML field name.
	 * @return array The pet data.
	 */
	public function pet_to_array( Pet $pet, string $output = ARRAY_A ): array {
		$pet_array    = array();
		$output_types = array( ARRAY_A, ARRAY_N );

		if ( empty( $output ) || ! in_array( $output, $output_types, true ) ) {
			$output = ARRAY_A;
		}

		foreach ( $pet->get_fields() as $field ) {
			if ( ARRAY_A === $output ) {
				$pet_array[ $this->decamelize( $field ) ] = $pet->get_field( $field );
			} else {
				$pet_array[] = array(
					'field' => $field,
					'value' => $pet->get_field( $field ),
				);
			}
		}

		return $pet_array;
	}

	/**
	 * Converts the XML returned by the API into a Pet instance.
	 *
	 * @param \SimpleXMLElement $xml Well-formatted XML for a pet.
	 * @return \BigWing\PetAndGo\Pet A new Pet instance.
	 */
	public function xml_to_pet( SimpleXMLElement $xml ): Pet {
		$pet  = new Pet();
		$kids = $xml->children();

		foreach ( $kids as $key => $value ) {
			$data = strval( $value );
			$data = trim( $data );
			$pet->set_field( $key, $data );
		}

		return $pet;
	}

	/**
	 * Convert the XML response from the API to an array of pet data.
	 *
	 * @param SimpleXMLElement $xml    The XML containing the pet fields.
	 * @param string           $xpath  The xpath to search for the pets collection.
	 * @param string           $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 *                                 ARRAY_N returns an array of rows indexed from 0.
	 *                                 ARRAY_A returns an associative array (key => value, ...),
	 *                                 with the key set to a snake_case version of the XML field name.
	 *                                 OBJECT and OBJECT_K return an instance of the Pet class with
	 *                                 variables keyed to the XML field name. Duplicate keys are discarded.
	 * @return array|\WP_Error An array of pet data, formatted according to $output.
	 */
	public function xml_to_pets( SimpleXMLElement $xml, string $xpath, $output = OBJECT ) {
		$pets     = array();
		$pets_xml = $xml->xpath( $xpath );

		if ( false === $pets_xml ) {
			$error_message = 'There was an error getting pet details.';
			$error_data    = array(
				'xpath' => $xpath,
				'xml'   => $xml,
			);

			$logger = new PGLogger();
			$logger->error(
				$error_message,
				$error_data
			);

			return new WP_Error( $logger::ERROR, $error_message, $error_data );
		}

		$output_types = array( ARRAY_A, ARRAY_N, OBJECT, OBJECT_K );
		if ( empty( $output ) || ! in_array( $output, $output_types, true ) ) {
			$output = OBJECT;
		}

		foreach ( $pets_xml as $pet_xml ) {
			$pet = $this->xml_to_pet( $pet_xml );

			if ( ARRAY_N === $output || ARRAY_A === $output ) {
				$pet = $this->pet_to_array( $pet, $output );
			}

			$pets[] = $pet;
		}

		return $pets;
	}
}
