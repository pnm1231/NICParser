<?php

declare(strict_types=1);

namespace pnm1231\NICParser;

use DateTime;
use pnm1231\NICParser\Exception\BadMethodCallException;
use pnm1231\NICParser\Exception\InvalidArgumentException;

class Builder
{
    private DateTime $birthday;
    private string $gender;
    private string $serialNumber;

    public function setParser(Parser $parser): void
    {
        $this->birthday = $parser->getBirthday();
        $this->gender = $parser->getGender();
        $this->serialNumber = $parser->getSerialNumber();
    }

    public function setBirthday(DateTime $date): self
    {
        $this->birthday = clone $date;

        return $this;
    }

    public function setGender(string $gender = 'M'): self
    {
        if ($gender === 'M' || $gender === 'F') {
            $this->gender = $gender;

            return $this;
        }

        throw new InvalidArgumentException('Unknown gender. Allowed values are: "M" and "F');
    }

    public function setSerialNumber(string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getNumber(): string
    {
        $this->checkBuilderFields();

        $year = $this->birthday->format('Y');

        $startDate = (new DateTime())->setDate((int)$year, 1, 1)->setTime(0, 0);

        $birthDateCount = (int) $this->birthday->diff($startDate)->format('%a');

        ++$birthDateCount;

        if ($this->gender === 'F') {
            $birthDateCount += 500;
        }

        $serial = $this->serialNumber;

        return "{$year}{$birthDateCount}{$serial}";
    }

    public function checkBuilderFields(): void
    {
        if (!isset($this->birthday)) {
            throw new BadMethodCallException('Attempting to build ID number without a valid birthday set.');
        }

        if (!isset($this->gender)) {
            throw new BadMethodCallException('Attempting to build ID number without a valid gender set.');
        }

        if (!isset($this->serialNumber)) {
            throw new BadMethodCallException('Attempting to build ID number without a valid serial number set.');
        }
    }
}
