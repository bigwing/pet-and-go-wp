<?php
/**
 * Message formatter to match WP style.
 *
 * @package BigWing\PetAndGo
 */

namespace BigWing\PetAndGo;

use Monolog\Formatter\LineFormatter;

/**
 * Class PGLineFormatter
 *
 * @package BigWing\PetAndGo
 */
class PGLineFormatter extends LineFormatter {

	/**
	 * PGLineFormatter constructor.
	 */
	public function __construct() {
		$message_format = "[%datetime%] %level_name%:  %message% %context% %extra\n";
		$date_format    = 'dd-MMM-yyyy HH:mm:ss z';

		parent::__construct( $message_format, $date_format, false, true );
	}
}
