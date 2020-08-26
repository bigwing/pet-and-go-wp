<?php
/**
 * Enum for getting speciesID
 *
 * @package BigWing\PetAndGo
 */

namespace BigWing\PetAndGo;

use MyCLabs\Enum\Enum;

/**
 * Class PGSpeciesEnum
 *
 * Enumerator with magic methods for getting the values.
 *
 * @link https://github.com/myclabs/php-enum
 *
 * @package BigWing\PetAndGo
 *
 * @psalm-immutable
 *
 * @method all()
 * @method dog()
 * @method cat()
 */
class PGSpeciesEnum extends Enum {
	/**
	 * Species ID for all species.
	 *
	 * @type int
	 */
	private const ALL = 0;
	/**
	 * Species ID for dogs.
	 *
	 * @type int
	 */
	private const DOG = 1;
	/**
	 * Species ID for cats.
	 *
	 * @type int
	 */
	private const CAT = 2;
}
