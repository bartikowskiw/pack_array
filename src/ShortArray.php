<?php
/**
 * Class Umlts\PackArray\ShortArray
 *
 */

declare( strict_types = 1 );
namespace Umlts\PackArray;

use Umlts\PackArray\PackArray;

/**
 * Signed short PackArray.
 */
class ShortArray extends PackArray {
    const PACK_FORMAT = 's';
    const PACK_BYTES = 2;
}
