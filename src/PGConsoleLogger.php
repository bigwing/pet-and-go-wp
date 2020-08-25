<?php
/**
 * Console logger using Monolog
 *
 * @package BigWing\PetAndGo
 */

namespace BigWing\PetAndGo;

use BigWing\PetAndGo\Dependencies\Monolog\Handler\BrowserConsoleHandler;
use BigWing\PetAndGo\Dependencies\Monolog\Logger;

/**
 * Class PGConsoleLogger
 *
 * @package BigWing\PetAndGo
 */
class PGConsoleLogger extends Logger {
	/**
	 * @var \BigWing\PetAndGo\PGConsoleLogger
	 */
	protected $console;

	/**
	 * PGConsoleLogger constructor.
	 *
	 * @param string $name Optional. The logger name.
	 */
	public function __construct( $name = 'PetAndGo:console' ) {
		parent::__construct( $name, [ new BrowserConsoleHandler() ] );

		$this->console = $this;

		return $this;
	}

	/**
	 * @param mixed $level
	 * @param mixed $message
	 * @param array $context
	 */
	public function log( $level, $message, array $context = [] ): void {
		if ( ! is_string( $message ) ) {
			$message = wp_json_encode( $message );
		}

		parent::log( $level, $message, $context );
	}
}
