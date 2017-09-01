<?php

declare( strict_types = 1 );

use PHPUnit\Framework\TestCase;
use Umlts\PackArray\ShortArray;


class ShortArrayTest extends TestCase {

    public function testCanBeCreated() {
        $a = new ShortArray();
        $this->assertInstanceOf( ShortArray::class, $a );
    }
}
