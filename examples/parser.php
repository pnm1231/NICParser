<?php

use pnm1231\NICParser\Parser;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Example 1
 */
$idNumber = '862348594v';

$parser = new Parser($idNumber);
$parser->getBirthday();// Returns a \DateTime object with the date parsed.

echo $parser->getBirthday()->format('Y-m-d'); // prints "1986-08-22"
echo $parser->getGender(); // Prints "M". M for male, F for female.
echo $parser->getSerialNumber(); // Prints "8594"

/**
 * Example 2
 */
$idNumber = '19935158154';

$parser = new Parser($idNumber);
$parser->getBirthday();// Returns a \DateTime object with the date parsed.

echo $parser->getBirthday()->format('Y-m-d'); // prints "1993-01-15"
echo $parser->getGender(); // Prints "F". M for male, F for female.
echo $parser->getSerialNumber(); // Prints "8154"
