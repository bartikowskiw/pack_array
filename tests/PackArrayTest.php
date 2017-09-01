<?php

declare( strict_types = 1 );

use PHPUnit\Framework\TestCase;
use Umlts\PackArray\PackArray;
use Umlts\PackArray\ShortArray;
use Umlts\PackArray\LongArray;
use Umlts\PackArray\LongLongArray;

class PackArrayClass extends PackArray {}

class PackArrayTest extends TestCase {

    public function testCanBeCreated() {
        $a = new PackArrayClass();
        $this->assertInstanceOf( PackArrayClass::class, $a );
    }

    public function testCountable() {
        $a = new PackArrayClass( [ 1,2,3,4,5,6,7,8 ] );
        $this->assertEquals( count( $a ), 8 );
    }

    public function testArrayAccess() {
        $a = new PackArrayClass();

        for ( $i=0; $i<10; $i++ ) { $a[] = $i; }
        $this->assertEquals( count( $a ), 10 );
        $this->assertEquals( $a[3], 3 );

        $a[3] = 42;
        $this->assertEquals( $a[3], 42 );
    }

    public function testIterator() {
        $a = new PackArrayClass( [ 1,2,3,4,5,6,7,8 ] );
        $sum_v = 0;
        $sum_k = 0;
        foreach ( $a as $k => $v ) {
            $sum_v += $v;
            $sum_k += $k;
        }
        // Check the values
        $this->assertEquals( $sum_v, 1+2+3+4+5+6+7+8 );
        // Check the keys
        $this->assertEquals( $sum_k, 0+1+2+3+4+5+6+7 );
    }

    public function testBasicFunctions() {
        $a = new PackArrayClass( [ 0,1,2,3 ] );

        $a->push( 42 );
        $this->assertEquals( $a->toArray(), [ 0, 1, 2, 3, 42 ] );
        $this->assertEquals( $a->pop(), 42 );
        $this->assertEquals( $a->toArray(), [ 0, 1, 2, 3 ] );

        $a->unshift( 42 );
        $this->assertEquals( $a->toArray(), [ 42, 0, 1, 2, 3 ] );
        $this->assertEquals( $a->shift(), 42 );
        $this->assertEquals( $a->toArray(), [ 0, 1, 2, 3 ] );

        $this->assertEquals( $a->get( 1 ), 1 );
        $a->set( 1, 42 );
        $this->assertEquals( $a->get( 1 ), 42 );
        $a->set( 1, 1 );

        $a->remove( 1 );
        $this->assertEquals( $a->toArray(), [ 0, 2, 3 ] );

        $this->assertEquals( $a->last(), 3 );
        $this->assertEquals( $a->first(), 0 );

    }

    public function testIntegerRange() {
        // PackArrays default is signed 64 bits. Check if it is safed
        // properly.
        $a = new PackArrayClass( [ PHP_INT_MAX, PHP_INT_MIN ] );
        $this->assertEquals( $a[0], PHP_INT_MAX );
        $this->assertEquals( $a[1], PHP_INT_MIN );
    }

    public function testExceptions() {
        $a = new PackArrayClass( [ 0,1,2,3 ] );

        $this->expectException( \OutOfBoundsException::class );
        $a->get( 10 );

        $this->expectException( \OutOfBoundsException::class );
        $a->get( -10 );

        $this->expectException( \OutOfBoundsException::class );
        $a->set( 10 );

        $this->expectException( \OutOfBoundsException::class );
        $a->remove( 10 );

        $this->expectException( \OutOfBoundsException::class );
        $a[10] = 1;
    }
}
