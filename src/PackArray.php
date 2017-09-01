<?php
/**
 * Class Umlts\PackArray\PackArray
 *
 */

declare( strict_types = 1 );
namespace Umlts\PackArray;

/**
 * Array of Integers saved in memory.
 *
 * Compared to native arrays or SplFixedArrays it is quite slow. The
 * upside is it's very low memory usage. Especially compared to normal
 * arrays.
 */
abstract class PackArray implements \Iterator, \Countable, \ArrayAccess {

    /**
     * Defines the representation of the values in memory.
     *
     * @see https://secure.php.net/manual/en/function.pack.php
     */
    const PACK_FORMAT = 'q';
    const PACK_BYTES = 8;

    /**
     * @var int
     *   Counts the number of saved Integers
     */
    private $length = 0;

    /**
     * Position variable for the implementation of the
     * Iterator interface.
     *
     * @var int
     */
    private $iterator_position = 0;

    /**
     * @var resource
     *   Pointer to the stream where the data is stored
     */
    private $fp;

    /**
     * Constructor
     *
     * @param array $array
     *   An array to fill the IntArray with
     * @param string $path
     *   Path to the stream used for storage. php://temp is default.
     *
     * @see https://secure.php.net/manual/en/wrappers.php.php
     */
    public function __construct( array $array = [], string $path = 'php://temp/maxmemory:20000000' ) {

        if ( $this::PACK_BYTES > PHP_INT_SIZE ) {
            trigger_error(
                'Cannot construct ' . get_class( $this ) . ': Your system and / or PHP version does not support ' . $this::PACK_BYTES * 8 . ' bit integers.',
                E_USER_ERROR
            );
        }

        $this->fp = fopen( $path, 'r+' );
        foreach ( $array as $v ) { $this->push( $v ); }
    }

    /**
     * Destructor.
     * Close the stream on object destruction
     */
    public function __destruct() {
        fclose( $this->fp );
    }

    /**
     * Returns the number of elements in the array.
     * Also implements the count() method for the Countable interface.
     *
     * @return int
     *   Returns number of elements
     */
    public function count() : int {
        return $this->length;
    }

    /**
     * Appends an element to the array.
     *
     * @param int $v
     * @returns self
     */
    public function push( int $v ) : self {
        $this->gotoEnd()->write( $v );
        $this->length++;
        return $this;
    }

    /**
     * Returns last element and removes it from the array.
     *
     * @returns int
     *   Returns last element
     */
    public function pop() : int {
        $v = $this->last();

        $this->length--;
        ftruncate( $this->fp, $this::PACK_BYTES * $this->length );

        return $v;
    }


    /**
     * Prepends an element to the array.
     *
     * @param int $v
     * @returns self
     */
    public function unshift( int $v ) : self {
        $this->push( 0 );
        for ( $i=$this->count()-1; $i>0; $i-- ) {
            $tmp = $this->get( $i-1 );
            $this->set( $i, $tmp );
        }
        $this->set( 0, $v );
        return $this;
    }

    /**
     * Returns first element and removes it from the array.
     *
     * @returns int
     *   Returns first element
     */
    public function shift() : int {
        $v = $this->first();

        for ( $i=0; $i<$this->count()-1; $i++ ) {
            $tmp = $this->get( $i+1 );
            $this->set( $i, $tmp );
        }
        $this->pop();

        return $v;
    }

    /**
     * Returns element at given position.
     *
     * @param int $i
     *   Index
     * @returns int
     *   Returns element
     */
    public function get( int $i ) : int {
        return $this->gotoIndex( $i )->read();
    }

    /**
     * Returns last element.
     *
     * @returns int
     *   Returns last element
     */
    public function last() {
        return $this->gotoIndex( $this->count() - 1 )->read();
    }

    /**
     * Returns first element.
     *
     * @returns int
     *   Returns first element
     */
    public function first() : int {
        return $this->gotoIndex( 0 )->read();
    }

    /**
     * Sets the value of an element.
     *
     * @param int $i
     *   Index of the element
     * @param int $v
     *   Value of the element
     * @returns self
     */
    public function set( int $i, int $v ) : self {
        $this->gotoIndex( $i )->write( $v );
        return $this;
    }

    /**
     * Removes element from array.
     *
     * @param int $i
     *   Index of the element to remove
     * @returns self
     */
    public function remove( int $i ) : self {
        $this->checkIndex( $i );
        for ( $j=$i; $i<$this->count()-1; $i++ ) {
            $this->set( $i, $this->get( $i+1 ) );
        }
        $this->pop();

        return $this;
    }

    /**
     * Reads the integer from current stream position.
     *
     * @returns int
     */
    private function read() : int {
        return $this->readMulti( 1 )[1];
    }

    /*
     * Reads $l integers from the current stream position.
     *
     * @param int $l
     *   Number of elements to read
     * @returns int
     */
    private function readMulti( int $l ) : array {
        if ( feof( $this->fp ) ) {
            throw new \OutOfBoundsException( 'Out of bounds / EOF reached.' );
        }
        $data = fread( $this->fp, $this::PACK_BYTES * $l );
        return unpack( $this::PACK_FORMAT . '*', $data );
    }

    /**
     * Writes integer at current stream position.
     *
     * @param int $v
     * @returns self
     */
    private function write( int $v ) : self {
        fwrite( $this->fp, pack( $this::PACK_FORMAT, (int) $v ) );
        return $this;
    }

    /**
     * Is the index valid?
     *
     * @param int $i
     *   Index
     * @returns bool
     *   Returns if the index is within the bounds
     */
    private function validIndex( int $i ) : bool {
        return $i >= 0 && $i < $this->count();
    }

    /**
     * Checks index, throws exception if it is invalid.
     *
     * @param int $i
     *   Index to check
     * @throws OutOfBoundsException
     */
    private function checkIndex( int $i ) : self{
        if ( !$this->validIndex( $i ) ) {
            throw new \OutOfBoundsException( 'Index out of bounds: ' . $i );
        }
        return $this;
    }


    /**
     * Moves stream pointer to the position of the given index.
     *
     * @param int $i
     * @return self
     */
    private function gotoIndex( int $i ) : self {
        $this->checkIndex( $i );
        fseek( $this->fp, $this::PACK_BYTES * $i );
        return $this;
    }

    /**
     * Moves stream pointer to the end of the stream. Data can be
     * apended there.
     *
     * @returns self
     */
    private function gotoEnd() : self {
        fseek( $this->fp, 0, SEEK_END );
        return $this;
    }

    /**
     * Returns a native array.
     *
     * @returns array
     */
    public function toArray() : array {
        $array = [];
        $this->gotoIndex( 0 );
        while ( $data = $this->readMulti( 1000 ) ) {
            foreach ( $data as $v ) { $array[] = $v; }
        }
        return $array;
    }

    /**
     * Magic function to display the contents of the object.
     *
     * @returns string
     */
    public function __toString() {
        $first = TRUE;
        $string = get_class( $this ) . '[ ';
        for ( $i=0; $i<$this->count(); $i++ ) {
            if ( !$first ) { $string .= ', '; }
            $string .= $this->get( $i );
            $first = FALSE;
        }
        $string .=  ' ]';
        $string .=  "\n";
        return $string;
    }

    // -----------------------------------------------------------------
    // Implementation of Iterator Interface
    // @see https://secure.php.net/manual/en/class.iterator.php
    // -----------------------------------------------------------------

    public function current() : int { return $this->get( $this->iterator_position ); }
    public function key() { return $this->iterator_position; }
    public function next() { $this->iterator_position++; }
    public function rewind() { $this->iterator_position = 0; }
    public function valid() { return $this->iterator_position < $this->count(); }

    // -----------------------------------------------------------------
    // Implementation of ArrayAccess Interface
    // @see https://secure.php.net/manual/en/class.arrayaccess.php
    // -----------------------------------------------------------------

    public function offsetExists( $offset ) : boolean { return $this->validIndex( $offset ); }
    public function offsetGet( $offset ) : int { return $this->get( $offset ); }
    public function offsetSet( $offset, $value ) {
        if ( $offset === NULL ) { // $a[] = $b;
            $this->push( $value );
        } else {
            $this->set( $offset, $value );
        }
    }
    public function offsetUnset( $offset ) { $this->remove( $offset ); }
}
