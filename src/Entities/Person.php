<?php
declare(strict_types=1);

namespace CakeDay\Entities;

use Carbon\CarbonInterface;

class Person
{
    private string $name;

    private CarbonInterface $dateOfBirth;

    public function __construct(string $name, CarbonInterface $dateOfBirth)
    {
        $this->setName($name);
        $this->setDateOfBirth($dateOfBirth);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDateOfBirth(): CarbonInterface
    {
        return clone $this->dateOfBirth; // we'll clone here so we dont accidentally mutate the dob later.
    }

    public function getBirthday(int $year)
    {
        $dateOfBirth = $this->getDateOfBirth();

        $day = $dateOfBirth->format('d');
        $month = $dateOfBirth->format('m');

        return $dateOfBirth->setDate($year, $month, $day);
    }

    public function setDateOfBirth(CarbonInterface $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getCakeDay(int $year): CarbonInterface
    {
        return $this->getBirthday($year)->addDay();
    }
}
