<?php

namespace pnm1231\NICParser\Tests;

use DateTime;
use pnm1231\NICParser\Builder;
use PHPUnit\Framework\TestCase;
use pnm1231\NICParser\Exception\BadMethodCallException;
use pnm1231\NICParser\Exception\InvalidArgumentException;
use pnm1231\NICParser\Parser;

class BuilderTest extends TestCase
{
    public function testBuilderFromArbitraryValues_Male(): void
    {
        $birthday = new DateTime();
        $birthday->setDate(1992, 9, 16);
        $birthday->setTime(0, 0);

        $builder = new Builder();
        $builder->setBirthday($birthday);
        $builder->setGender();
        $builder->setSerialNumber(25738);

        $this->assertSame('199226025738', $builder->getNumber());

        $parser = new Parser('199226025738');

        $newBuilder = new Builder();
        $newBuilder->setParser($parser);

        $this->assertSame('199226025738', $builder->getNumber());
    }

    public function testBuilderFromArbitraryValues_GenderNumberAdjustment(): void
    {
        $birthday = new DateTime();
        $birthday->setDate(1992, 9, 16);
        $birthday->setTime(0, 0);

        $builder = new Builder();
        $builder->setBirthday($birthday);
        $builder->setGender('F');
        $builder->setSerialNumber(25738);

        $this->assertSame('199276025738', $builder->getNumber());
    }

    public function testInvalidArguments(): void
    {
        $builder = new Builder();
        $builder->setGender('F');
        $builder->setGender();

        $this->expectException(InvalidArgumentException::class);
        $builder->setGender('T');
    }

    public function testInsufficientData_Birthday(): void
    {
        $builder = new Builder();
        $this->expectException(BadMethodCallException::class);
        $builder->getNumber();
    }

    public function testInsufficientData_Gender(): void
    {
        $birthday = new DateTime();
        $birthday->setDate(1992, 9, 16);
        $birthday->setTime(0, 0);

        $builder = new Builder();
        $builder->setBirthday($birthday);
        $this->expectException(BadMethodCallException::class);
        $builder->getNumber();
    }

    public function testInsufficientData_SerialNumber(): void
    {
        $birthday = new DateTime();
        $birthday->setDate(1992, 9, 16);
        $birthday->setTime(0, 0);

        $builder = new Builder();
        $builder->setBirthday($birthday);
        $builder->setGender();
        $this->expectException(BadMethodCallException::class);
        $builder->getNumber();
    }
}
