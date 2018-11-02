<?php

declare( strict_types = 1 );
namespace Umlts\PackArray\Test;

use \PHPUnit\Framework\TestCase;
use \Umlts\PackArray\ShortArray;

class ShortArrayTest extends TestCase {

    public function testCanBeCreated() {
        $a = new ShortArray();
        $this->assertInstanceOf( ShortArray::class, $a );
    }
}
