<?php
/**
 * PSR-compliant file logger
 *
 * @package BigWing\PetAndGo
 */

namespace BigWing\PetAndGo;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Logger
 *
 * @package BigWing\PetAndGo
 */
class PGLogger extends Logger {
	/**
	 * Logger constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'PetAndGo' ) {
		parent::__construct( $name );

		try {
			$this->setup_file_logging();
		} catch ( Exception $e ) {
			$this->error( $e->getMessage() );
		}

		return $this;
	}

	/**
	 * Sets up the log file handler to local debug log.
	 *
	 * @throws \Exception
	 */
	private function setup_file_logging() {
		$log_path = WP_CONTENT_DIR . '/debug.log';

		if ( defined( WP_DEBUG_LOG ) && is_string( WP_DEBUG_LOG ) ) {
			$log_path = WP_DEBUG_LOG;
		}

		/**
		 * Filter the log path for the Pet and Go logs.
		 *
		 * @param string|null|bool $log_path The path to the log file.
		 *                                   Set to empty, null, or false to bypass log file.
		 */
		$log_path = apply_filters( 'bw_pgapi_log_path', $log_path );

		if ( ! empty( $log_path ) ) {
			$handler = new StreamHandler( $log_path );
			$handler->setFormatter( new PGLineFormatter() );
			$this->pushHandler( $handler );
		}
	}

}
