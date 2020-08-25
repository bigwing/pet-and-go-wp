<?php
/**
 * The HTTP client
 *
 * @package BigWing\PetAndGo
 */

namespace BigWing\PetAndGo;

/**
 * Interface ClientInterface
 *
 * Define the HTTP or SOAP client used in external requests.
 *
 * @package BigWing\PetAndGo
 */
interface ClientInterface {

	/**
	 * Gets an instance of the client.
	 *
	 * @return mixed The client instance.
	 */
	public function get_client();

	/**
	 * Sets up the client.
	 *
	 * @param mixed $client The client.
	 */
	public function set_client( $client );
}
