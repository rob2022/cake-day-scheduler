<?php
declare(strict_types=1);

namespace CakeDay\Entities;

use Carbon\CarbonInterface;

class Day
{
    private CarbonInterface $date;

    /**
     * @var Person[]
     */
    private array $people = [];

    public function __construct(CarbonInterface $date)
    {
        $this->date = $date->copy()->startOfDay();
    }

    public function addPerson(Person $person): void
    {
        $this->people[] = $person;
    }

    public function addPeople(Person ...$people): void
    {
        foreach ($people as $person) {
            $this->addPerson($person);
        }
    }

    /**
     * @return Person[]
     */
    public function getPeople(): array
    {
        return $this->people;
    }

    public function isCakeDay(): bool
    {
        return (bool) $this->getPeople();
    }

    public function getSizeOfCake(): string
    {
        if (!$this->getPeople()) {
            throw new \UnexpectedValueException('No cake');
        }

        return count($this->getPeople()) === 1 ? 'small' : 'large';
    }

    public function getDate(): CarbonInterface
    {
        return $this->date;
    }

    public function removePeople(): void
    {
        $this->people = [];
    }

    public function isSmallCakeDay(): bool
    {
        return count($this->people) === 1;
    }

    public function isLargeCakeDay(): bool
    {
        return count($this->people) > 1;
    }
}
