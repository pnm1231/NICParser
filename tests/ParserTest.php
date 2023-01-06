<?php

namespace pnm1231\NICParser\Tests;

use Ayesh\CaseInsensitiveArray\Strict;
use pnm1231\NICParser\Exception\InvalidArgumentException;
use pnm1231\NICParser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function getInvalidSamples(): array
    {
        $data = [];

        $data['201626085730v'] = ['201626085730v', 102]; // should not be a V at end.
        $data['187526085730'] = ['187526085730', 200]; // year out of accepted range.
        $data['20162608573v'] = ['20162608573v', 102]; // should not be a V at end.
        $data['922608573x'] = ['922608573x', 103]; // x is invalid.
        $data[] = [(string)time(), 103]; // invalid char at end ().
        $data['foobar'] = ['foobar', 102]; // invalid length.
        $data['abcdepoghtyd'] = ['abcdepoghtyd', 102]; // invalid chars.
        $data[] = ['', 102]; // invalid chars.
        $data['ninechars'] = ['ninechars', 102]; // 9 chars is valid, but not all-int
        $data['929782220v'] = ['929782220v', 201]; // Date overflow, female.
        $data['929782220V'] = ['929782220V', 201]; // Date overflow, female.
        $data['X297R2220V'] = ['X29782220V', 102]; // Date overflow, female.

        return $data;
    }

    public function getValidSamples(): Strict
    {
        $data = new Strict();

        $data['670110320V'] = ['670110320V', ['year' => 1967, 'month' => 1, 'date' => 11, 'serial' => '0320', 'gender' => 'M', 'format' => 1]];
        $data['922602573v'] = ['922602573v', ['year' => 1992, 'month' => 9, 'date' => 16, 'serial' => '2573', 'gender' => 'M', 'format' => 1]];
        $data['922602573'] = ['922602573', ['year' => 1992, 'month' => 9, 'date' => 16, 'serial' => '2573', 'gender' => 'M', 'format' => 1]];
        $data['913014146V'] = ['913014146V', ['year' => 1991, 'month' => 10, 'date' => 27, 'serial' => '4146', 'gender' => 'M', 'format' => 1]];
        $data['902580972V'] = ['902580972V', ['year' => 1990, 'month' => 9, 'date' => 14, 'serial' => '0972', 'gender' => 'M', 'format' => 1]];
        $data['980011550V'] = ['980011550V', ['year' => 1998, 'month' => 1, 'date' => 1, 'serial' => '1550', 'gender' => 'M', 'format' => 1]];
        $data['199336578548'] = ['199336578548', ['year' => 1993, 'month' => 12, 'date' => 30, 'serial' => '78548', 'gender' => 'M', 'format' => 2]];
        $data['199236578548'] = ['199236578548', ['year' => 1992, 'month' => 12, 'date' => 30, 'serial' => '78548', 'gender' => 'M', 'format' => 2]];
        $data['199136578548'] = ['199136578548', ['year' => 1991, 'month' => 12, 'date' => 30, 'serial' => '78548', 'gender' => 'M', 'format' => 2]];
        $data['199226025738'] = ['199226025738', ['year' => 1992, 'month' => 9, 'date' => 16, 'serial' => '25738', 'gender' => 'M', 'format' => 2]];
        $data['200505100170'] = ['200505100170', ['year' => 2005, 'month' => 2, 'date' => 20, 'serial' => '00170', 'gender' => 'M', 'format' => 2]];
        $data['201626085734'] = ['201626085734', ['year' => 2016, 'month' => 9, 'date' => 16, 'serial' => '85734', 'gender' => 'M', 'format' => 2]];

        return $data;
    }

    /**
     * @dataProvider getInvalidSamples
     */
    public function testValidityChecker(string $id, int $expectedErrorCode): void
    {
        $this->expectException(InvalidArgumentException::class);

        if ($expectedErrorCode) {
            $this->expectExceptionCode($expectedErrorCode);
        }
        new Parser($id);
    }

    /**
     * @dataProvider getValidSamples
     */
    public function testIndividualFields(string $id, array $actualData = []): void
    {
        $parser = new Parser($id);

        $date = $parser->getBirthday();

        $this->assertSame($actualData['year'], (int) $date->format('Y'));
        $this->assertSame($actualData['month'], (int) $date->format('n'));
        $this->assertSame($actualData['date'], (int) $date->format('j'));
        $this->assertSame($actualData['gender'], $parser->getGender());
        $this->assertSame($actualData['serial'], $parser->getSerialNumber());
        $this->assertSame($actualData['format'], $parser->getFormat());
    }
}
