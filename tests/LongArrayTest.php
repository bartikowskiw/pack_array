<?php

declare( strict_types = 1 );

use PHPUnit\Framework\TestCase;
use Umlts\PackArray\LongArray;

class LongArrayTest extends TestCase {

    public function testCanBeCreated() {
        $a = new LongArray();
        $this->assertInstanceOf( LongArray::class, $a );
    }
}
