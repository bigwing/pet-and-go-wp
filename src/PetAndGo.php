<?php
/**
 * API for the PetandGo.com Web Services
 *
 * @author BigWing <wordpress@bigwing.com>
 */

namespace BigWing\PetAndGo;

use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use WP_Error;
use WP_Http;

/**
 * Class PetAndGo
 *
 * @package BigWing\PetAndGo
 */
class PetAndGo implements LoggerAwareInterface, ApiInterface, ClientInterface {

	use LoggerAwareTrait;
	use Utilities;

	/**
	 * @var string
	 */
	public static $version = '1.0.0';

	/**
	 * @var string $api_url The URL to the PetAndGo API.
	 */
	protected $api_url;

	/** @var string $auth_token The API authentication token. */
	protected $auth_token;

	/** @var PetAndGo $instance The singleton instance. */
	public static $instance = null;

	/**
	 * @var
	 */
	public $client;

	/**
	 * @var array
	 */
	protected $settings = array(
		'base_url'       => 'https://ws.petango.com/webservices/wsadoption.asmx',
		'transport_opts' => array(),
	);

	/**
	 * @var string[]
	 */
	protected $search_defaults = array(
		'authkey'        => '',
		'speciesID'      => '0',
		'sex'            => 'A',
		'ageGroup'       => 'All',
		'location'       => '',
		'site'           => '',
		'onHold'         => 'A',
		'orderBy'        => 'ID',
		'primaryBreed'   => 'All',
		'secondaryBreed' => 'All',
		'specialNeeds'   => '',
		'noDogs'         => '',
		'noCats'         => '',
		'noKids'         => '',
		'stageID'        => '',
	);

	/**
	 * @var \BigWing\PetAndGo\PGConsoleLogger
	 */
	protected $console;

	/**
	 * Initializes a new PetAndGo instance.
	 * You can optionally turn on debugging for all requests by setting debug to true.
	 *
	 * @param string $auth_key The API key provided by PetAndGo.com.
	 * @param array  $options  [optional]
	 *                         Options to configure the PetAndGo instance.
	 *                         base_url - the API URL. Default https://ws.petango.com/webservices/wsadoption.asmx.
	 *
	 * @throws \Exception Throws exception if any required dependencies are missing
	 */
	public function __construct( string $auth_key, array $options = array() ) {
		$this->settings = wp_parse_args( $options, $this->get_settings() );

		$this->set_auth_token( $auth_key );
		$this->set_api_url( $this->get_settings( 'base_url' ) );

		$this->console = new PGConsoleLogger();
		$this->setLogger( new PGLogger() ); // sets $logger class variable using our implementer.

		self::$instance = $this;

		return $this;
	}

	/**
	 * @return \BigWing\PetAndGo\PetAndGo|null
	 */
	public static function get_instance() {
		// doesn't follow the singleton pattern since we can't instantiate a new one on the fly.
		return self::$instance;
	}

	/**
	 * Gets the client used for requests.
	 *
	 * @return \WP_Http
	 */
	public function get_client(): WP_Http {
		if ( ! $this->client instanceof WP_Http ) {
			$this->set_client( new WP_Http() );
		}

		return $this->client;
	}

	/**
	 * Sets the client used for requests.
	 *
	 * @param mixed $client
	 */
	public function set_client( $client ) {
		$this->client = $client;
	}

	/**
	 * @return string
	 */
	public function get_api_url(): string {
		return $this->api_url;
	}

	/**
	 * @param string $api_url
	 */
	public function set_api_url( string $api_url ): void {
		$this->api_url = esc_url_raw( $api_url, [ 'http', 'https' ] );
	}

	/**
	 * @return string
	 */
	public function get_auth_token(): string {
		return $this->auth_token;
	}

	/**
	 * @param string $auth_token
	 */
	public function set_auth_token( $auth_token ): void {
		$this->auth_token = strval( $auth_token );
	}

	/**
	 * Query the API with given parameters.
	 *
	 * @param array $query_args The args passed to the API request.
	 * @param array $options    Options passed to the transport client.
	 * @return string The query response.
	 */
	public function query( array $query_args = array(), array $options = array() ): string {
		$query_args['authkey'] = $this->get_auth_token();

		$endpoint = '';
		if ( ! empty( $options['endpoint'] ) ) {
			$endpoint = $options['endpoint'];
		}
		$url = $this->get_endpoint_url( $endpoint );

		$http_args = array(
			'method'      => 'POST',
			'httpversion' => '1.1',
			'headers'     => array(
				'content-type' => 'application/x-www-form-urlencoded',
			),
			'compress'    => true,
			'body'        => $query_args,
		);
		$http_args = wp_parse_args( $options, $http_args );

		if ( empty( $url ) ) {
			$msg = sprintf( 'Invalid URL provided in %1$s: %2$s.', __METHOD__, $url );
			$this->logger->error( $msg );

			return '';
		}

		$response = $this->get_client()->request( $url, $http_args );

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * @param string $endpoint
	 * @return string
	 */
	public function get_endpoint_url( string $endpoint = '' ): string {
		$url = untrailingslashit( $this->get_api_url() );

		if ( ! empty( $endpoint ) ) {
			$url = sprintf( '%1$s/%2$s', $url, ltrim( $endpoint, " /" ) );
		}

		$url = wp_http_validate_url( $url );

		return false !== $url ? $url : '';
	}


	/**
	 * @param string $name
	 * @return array|mixed
	 */
	public function get_settings( $name = '' ) {
		$settings = $this->settings;

		if ( empty( $name ) || ! array_key_exists( $name, $settings ) ) {
			return $settings;
		}

		return $settings[ $name ];
	}

	/**
	 * @param array $args
	 * @return array
	 */
	public function get_search_args( array $args = array() ): array {
		$search_args = wp_parse_args( $args, $this->search_defaults );

		return array_map( 'strval', $search_args );
	}


	/**
	 * @param string $species
	 * @return int
	 */
	private function get_species_id( string $species = 'all' ): int {
		$species = strtoupper( $species );

		if ( ! $this->is_valid_species( $species ) ) {
			return 0;
		}

		return PGSpeciesEnum::$species()->getValue();
	}

	/**
	 * @param string $species
	 * @return bool
	 */
	private function is_valid_species( string $species ): bool {
		return PGSpeciesEnum::isValidKey( strtoupper( $species ) );
	}

	/**
	 * Get the pets from the AdoptableSearch query.
	 *
	 * @uses PetAndGo::get_adoptable_pets()
	 *
	 * @param string $species    Optional. The species to search. Default 'all'.
	 * @param array  $query_args Optional. Query args for the request. Default to common set of args.
	 * @return array The results of the search, or empty array if there's an error.
	 */
	public function get_pets( string $species = 'all', array $query_args = array() ): array {
		return $this->remember_transient(
			'adoptable_pets_' . $species,
			function () use ( $species, $query_args ) {
				return $this->get_pets_cb( $species, $query_args );
			},
			15 * MINUTE_IN_SECONDS
		);
	}

	/**
	 * Get the pets from the AdoptableSearch query.
	 *
	 * @internal
	 * @param string $species    The species to search.
	 * @param array  $query_args Query args for the request.
	 * @return array|WP_Error The results of the search, or error if there's any problems.
	 */
	private function get_pets_cb( string $species = 'all', array $query_args = array() ) {
		$endpoint = 'AdoptableSearch';

		if ( ! $this->is_valid_species( $species ) ) {
			$error_message = sprintf( 'Invalid species given for %s', $endpoint );
			$error_data    = array(
				'species'    => $species,
				'query_args' => $query_args,
			);

			$this->logger->error( $error_message, $error_data );

			return new WP_Error( Logger::ERROR, $error_message, $error_data );
		}

		// Ensure speciesID is normalized for the query, which requires a string-ified number.
		if ( ! isset( $query_args['speciesID'] ) ) {
			$query_args['speciesID'] = $this->get_species_id( $species );
		}
		$query_args = $this->get_search_args( $query_args );

		$response = $this->query( $query_args, array( 'endpoint' => $endpoint ) );

		if ( empty( $response ) ) {
			$error_message = sprintf( 'No results from %s', $endpoint );
			$error_data    = array(
				'species'    => $species,
				'query_args' => $query_args,
			);

			$this->logger->notice( $error_message, $error_data );

			return new WP_Error( Logger::NOTICE, $error_message, $error_data );
		}

		$xml = simplexml_load_string( $response );

		return ( new PGParser() )->xml_to_pets( $xml, $this->get_xpath_from_endpoint( $endpoint ) );
	}

	/**
	 * @param $pet_id
	 *
	 * @return Pet
	 */
	public function get_pet( string $pet_id ): Pet {
		return $this->remember_transient(
			'pet_details_' . $pet_id,
			function () use ( $pet_id ) {
				return $this->get_pet_cb( $pet_id );
			},
			15 * MINUTE_IN_SECONDS
		);
	}

	/**
	 * Gets the data for a single pet.
	 *
	 * @param string $pet_id
	 * @return Pet|\WP_Error An instance of Pet for this animal, or error if XML can't be parsed.
	 */
	private function get_pet_cb( string $pet_id ) {
		$endpoint = 'AdoptableDetails';

		$response     = $this->query( array( 'animalID' => $pet_id ), array( 'endpoint' => $endpoint ) );
		$xml_response = simplexml_load_string( $response );

		$pet_details = $xml_response->xpath( $this->get_xpath_from_endpoint( $endpoint ) );

		if ( false === $pet_details ) {
			$error_message = 'There was an error getting pet details.';
			$error_data    = array(
				'endpoint' => $endpoint,
				'response' => $response,
				'xml'      => $xml_response,
			);

			$this->logger->error(
				$error_message,
				$error_data
			);

			return new WP_Error( Logger::ERROR, $error_message, $error_data );
		}

		return ( new PGParser() )->xml_to_pet( $pet_details[0] );
	}

}
