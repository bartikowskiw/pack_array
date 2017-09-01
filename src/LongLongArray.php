<?php

declare( strict_types = 1 );
namespace Umlts\PackArray;

use Umlts\PackArray\PackArray;

/**
 * Signed long long PackArray.
 */
class LongLongArray extends PackArray {
    const PACK_FORMAT = 'q';
    const PACK_BYTES = 8;
}
