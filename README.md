# Sri Lankan National Identity Card Number Parser
A PHP library to parse, validate, and generate valid Sri Lankan national identity card numbers.

[![Latest Stable Version](http://poser.pugx.org/pnm1231/nic-parser/v)](https://packagist.org/packages/pnm1231/nic-parser)
[![Total Downloads](http://poser.pugx.org/pnm1231/nic-parser/downloads)](https://packagist.org/packages/pnm1231/nic-parser)
[![Latest Unstable Version](http://poser.pugx.org/pnm1231/nic-parser/v/unstable)](https://packagist.org/packages/pnm1231/nic-parser)
[![License](http://poser.pugx.org/pnm1231/nic-parser/license)](https://packagist.org/packages/pnm1231/nic-parser)
[![PHP Version Require](http://poser.pugx.org/pnm1231/nic-parser/require/php)](https://packagist.org/packages/pnm1231/nic-parser)

### Installation

You can install the library easily with composer. Requires PHP 7.4 or later.

``` composer require pnm1231/nic-parser```

Alternatively, you can download the library from GitHub, and manually include the class or integrate into your own autoloader. See the included `composer.json` file for `PSR-4` namespace mappings.

### Usage

See the [Wikipedia article](https://en.wikipedia.org/wiki/National_identity_card_%28Sri_Lanka%29) for the formats used.

#### Parsing an ID number

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

#### Validating an ID number
The `Parser` class throws an exception when you instantiate it with an invalid ID number. Make sure you always catch exceptions on validation.

    <?php
    use pnm1231\NICParser\Parser;
    use pnm1231\NICParser\Exception\InvalidArgumentException;
    
    require_once __DIR__ . '/../vendor/autoload.php';
    
    /* This is an invalid ID number because 499 here is not indicating a valid
    birth date */
    $idNumber = '924998593v';
    
    try {
      $parser = new Parser($idNumber);
    }
    catch (\pnm1231\NICParser\Exception\InvalidArgumentException $exception) {
      echo $exception->getMessage(); // "Birthday indicator is invalid."
    }

Depending on the validation error, you will get different messages explaining the situation. All exceptions will be instances of `pnm1231\NICParser\Exception\InvalidArgumentException`.

#### Building an NIC number

    <?php
    
    use pnm1231\NICParser\Builder;
    
    require_once __DIR__ . '/../vendor/autoload.php';
    
    $birthday = new \DateTime();
    $birthday->setDate(1992, 9, 16);
    $birthday->setTime(0, 0);
    
    $builder = new Builder();
    $builder->setBirthday($birthday);
    $builder->setGender('M'); // M for male, F for female.
    $builder->setSerialNumber(25738);
    
    echo $builder->getNumber(); // "199226025738". This is the new format.

### Contribute
All contributions are welcome. If you have any questions, please post an issue in the GitHub. For any PRs, we'd appreciate if you can add proper test coverage as well. 

### Alternative Implementations
 - [Ksengine/NICParser](https://github.com/Ksengine/NICParser/) - A Python implementation
