<?php
/**
 * The API methods used to interact with the PetAndGo API.
 *
 * @package BigWing\PetAndGo
 */

namespace BigWing\PetAndGo;

/**
 * Interface ApiInterface
 *
 * @package BigWing\PetAndGo
 */
interface ApiInterface {

	/**
	 * Gets the API URL.
	 *
	 * @return string
	 */
	public function get_api_url(): string;

	/**
	 * Sets the API URL.
	 *
	 * @param string $api_url The base URL for the API.
	 */
	public function set_api_url( string $api_url ): void;

	/**
	 * Gets the API authorization token/key.
	 *
	 * @return string The token used for 'authkey' authentication.
	 */
	public function get_auth_token(): string;

	/**
	 * Sets the API authorization token/key.
	 *
	 * @param string $auth_token The token used for 'authkey' authentication.
	 */
	public function set_auth_token( string $auth_token ): void;

	/**
	 * Queries the API with the client transport.
	 *
	 * @param array $query_args Parameters passed to the API from the query.
	 * @param array $options Options passed to the client transport.
	 * @return mixed The response as determined by the API and the transport.
	 */
	public function query( array $query_args = array(), array $options = array() );
}
