<?php
/**
 * Class Umlts\PackArray\LongArray
 *
 */

declare( strict_types = 1 );
namespace Umlts\PackArray;

use Umlts\PackArray\PackArray;

/**
 * Signed long PackArray.
 */
class LongArray extends PackArray {
    const PACK_FORMAT = 'l';
    const PACK_BYTES = 4;
}
