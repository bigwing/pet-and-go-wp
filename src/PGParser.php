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
	 * Converts a pet class to an array.
	 *
	 * @param \BigWing\PetAndGo\Pet $pet      The Pet to convert.
	 * @param string                $key_type Optional. 'snake' converts keys to WP-standard snake_case.
	 *                                        'camel' keeps the CamelCased field names returned by the API.
	 * @return array The pet data.
	 */
	public function pet_to_array( Pet $pet, $key_type = 'camel' ): array {
		$output_types = array( 'snake', 'camel' );
		$pet_array    = $pet->get_pet_details_array();

		if ( empty( $key_type ) || ! in_array( strtolower( $key_type ), $output_types, true ) ) {
			$key_type = 'camel';
		}

		if ( 'snake' === $key_type ) {
			$s = array();

			foreach ( $pet_array as $key => $value ) {
				$sp[ $this->decamelize( $key ) ] = $value;
				unset( $pet_array[ $key ] );
			}

			return $pet_array = $s;
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
	 * @param SimpleXMLElement $xml   The XML containing the pet fields.
	 * @param string           $xpath The xpath to search for the pets collection.
	 * @return array|\WP_Error An array of pet data, formatted according to $output.
	 */
	public function xml_to_pets( SimpleXMLElement $xml, string $xpath ) {
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

		foreach ( $pets_xml as $pet_xml ) {
			$pets[] = $this->xml_to_pet( $pet_xml );
		}

		return $pets;
	}
}
