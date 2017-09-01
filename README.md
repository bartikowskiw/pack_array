# Class PackArray

Memory efficient array class storing integer values.

PHP arrays are veeery memory hungry. This implementation uses PHP's
[pack()](https://secure.php.net/manual/en/function.pack.php) and
[unpack()](https://secure.php.net/manual/en/function.unpack.php)
functions to store the values in memory. This makes the array much
smaller than PHP's native ones.

The cost for low memory usage is slow speed. If speed is important
PHP's [SplFixedArray](https://secure.php.net/manual/en/class.spldoublylinkedlist.php)
or
[SplDoublyLinkedList](https://secure.php.net/manual/en/class.splfixedarray.php)
classes could be helpful.

## Classes

There are different classes for different integer formats:

- *ShortArray*, signed short (16 bit, machine byte order)
- *LongArray*, signed long (32 bit, machine byte order)
- *LongLongArray*, signed long long (64 bit, machine byte order)

Some PHP versions and OSes may not support 64 bit integers. Creating a
LongLongArray on a 32 bit systems triggers a PHP E_USER_ERROR error.

The integer size in bytes can be determined using the
[PHP_INT_SIZE](http://www.php.net/manual/en/language.types.integer.php)
constant.

## Usage

The PackArrays can be mostly used like normal arrays. They implement
the Count, Iterable and ArrayAccess interfaces.

```php
// Create new object for 32bit integers
$a = new LongArray( [ 1,2,3,4,5 ] );

// ArrayAccess
echo $a[1];             // = 2

// Countable
echo count( $a );       // = 5

// and Iterable:
foreach ( $a as $k => $v ) {
    echo "$k: $v\n";
}
```
