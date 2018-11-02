<?php

declare( strict_types = 1 );
namespace Umlts\PackArray\Test;

use \PHPUnit\Framework\TestCase;
use \Umlts\PackArray\LongLongArray;

class LongLongArrayTest extends TestCase {

    public function testCanBeCreated() {
        // Do not test, if 64bit is not supported
        if ( PHP_INT_SIZE < 8 ) { return; }

        $a = new LongLongArray();
        $this->assertInstanceOf( LongLongArray::class, $a );
    }
}
