<?php
declare(strict_types=1);

namespace CakeDay\Services\Writers;

use CakeDay\Collections\CakeDaySchedule;
use CakeDay\Entities\Day;
use CakeDay\Entities\Person;

abstract class CakeDayAbstractWriter
{
    abstract protected function writeRows(array $rows): void;

    public function write(CakeDaySchedule $cakeDayCollection): void
    {
        $rows = $this->getRows($cakeDayCollection);
        $this->writeRows($rows);
    }

    protected function getRows(CakeDaySchedule $cakeDayCollection): array
    {
        $rows = array_map(function(Day $day) {
            return [
                $day->getDate()->format('Y-m-d'),
                $day->isSmallCakeDay() ? 1 : 0,
                $day->isLargeCakeDay() ? 1 : 0,
                implode(', ', array_map(fn(Person $person) => $person->getName(), $day->getPeople()))
            ];
        }, $cakeDayCollection->toArray());

        return [
            ['Date', 'Number of Small Cakes', 'Number of Large Cakes', 'Names of people'],
            ...$rows
        ];
    }
}
