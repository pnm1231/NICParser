<?php

declare(strict_types=1);

namespace pnm1231\NICParser;

use DateInterval;
use DateTime;
use pnm1231\NICParser\Exception\InvalidArgumentException;

class Parser
{
    public const ID_FORMAT_PRE_2016 = 1;
    public const ID_FORMAT_2016 = 2;
    private array $dataComponents = [];

    public function __construct(string $idNumber)
    {
        $this->parse($idNumber);
    }

    private function parse(string $idNumber): void
    {
        $idNumber = $this->checkLength($idNumber);

        $this->checkBirthDate($idNumber);

        $this->detectFormat($idNumber);
    }

    private function checkLength(string $idNumber): string
    {
        $idNumber = strtoupper($idNumber);

        $strlen = strlen($idNumber);

        if ($strlen === 10) {
            if ($idNumber[9] !== 'V') {
                throw new InvalidArgumentException('Ending character is invalid.', 103);
            }

            $idNumber = substr($idNumber, 0, 9);
        }

        if (!ctype_digit($idNumber)) {
            throw new InvalidArgumentException('Provided number is not all-numeric', 102);
        }

        return $idNumber;
    }

    private function checkBirthDate(string $idNumber): void
    {
        $fullNumber = strlen($idNumber) === 9
            ? '19' . $idNumber
            : $idNumber;

        $year = (int) substr($fullNumber, 0, 4);

        $this->dataComponents['year'] = $year;

        $this->checkBirthYear($year);

        $this->buildBirthDateObject($fullNumber, $year);

        $this->dataComponents['serial'] = substr($fullNumber, 7);
    }

    private function checkBirthYear(int $year): void
    {
        if ($year < 1900 || $year > 2100) {
            throw new InvalidArgumentException('Birth year is out ff 1900-2100 range', 200);
        }
    }

    private function buildBirthDateObject(string $fullNumber, int $year): void
    {
        $birthday = new DateTime();

        $birthday->setDate($year, 1, 1)->setTime(0, 0);

        $birthDaysSince = (int) substr($fullNumber, 4, 3);

        if ($birthDaysSince >= 500) {
            $birthDaysSince -= 500;
            $this->dataComponents['gender'] = 'F';
        } else {
            $this->dataComponents['gender'] = 'M';
        }

        --$birthDaysSince;

        if ($birthDaysSince > (31 + 28) && date('L', mktime(0, 0, 0, 1, 1, $year)) !== '1') {
            --$birthDaysSince;
        }

        $birthday->add(new DateInterval('P' . $birthDaysSince . 'D'));

        $this->dataComponents['date'] = $birthday;

        if ($birthday->format('Y') !== (string) $year) {
            throw new InvalidArgumentException('Birthday indicator is invalid.', 201);
        }
    }

    private function detectFormat(string $idNumber): void
    {
        $this->dataComponents['format'] = strlen($idNumber) === 12
            ? static::ID_FORMAT_2016
            : static::ID_FORMAT_PRE_2016;
    }

    public function getBirthday(): DateTime
    {
        return $this->dataComponents['date'];
    }

    public function getSerialNumber(): string
    {
        return $this->dataComponents['serial'];
    }

    public function getFormat(): int
    {
        return $this->dataComponents['format'];
    }

    public function getGender(): string
    {
        return $this->dataComponents['gender'];
    }
}
