<?php

use pnm1231\NICParser\Exception\InvalidArgumentException;
use pnm1231\NICParser\Parser;

require_once __DIR__ . '/../vendor/autoload.php';

/* This is an invalid ID number because 499 here is not indicating a valid birthdate */
$idNumber = '924998593v';

try {
    $parser = new Parser($idNumber);
} catch (InvalidArgumentException $exception) {
    echo $exception->getMessage(); // "Birthday indicator is invalid."
}
