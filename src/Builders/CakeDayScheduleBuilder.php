<?php
declare(strict_types=1);

namespace CakeDay\Builders;

use CakeDay\Collections\CakeDaySchedule;
use CakeDay\Collections\PersonCollection;
use CakeDay\Entities\Day;
use CakeDay\Entities\Person;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;

class CakeDayScheduleBuilder
{
    private const WEEKDAYS = [1, 2, 3, 4, 5];

    public function build(
        CarbonInterface $startDate,
        PersonCollection $personCollection
    ): CakeDaySchedule {
        // Get an array containing the Day class for each day of the year these are keyed by day of the year.
        $daysForYears = $this->getDaysForYear($startDate);

        $workDaysForYear = $this->removeNonWorkDays(...$daysForYears);

        $cakeDaySchedule = [];
        foreach ($personCollection as $person) {
            $cakeDaySchedule = $this->addBirthDayToNextAvailableWorkDay($workDaysForYear, $startDate->year, $person);
        }

        $cakeDaySchedule = $this->reassignForHealthReasons(...$cakeDaySchedule);

        $cakeDaySchedule = array_filter($cakeDaySchedule, fn(Day $day) => $day->isCakeDay());

        // Remove non cake days
        return new CakeDaySchedule($cakeDaySchedule);
    }

    /**
     * @param Day[] $workDays
     * @param int $year
     * @param Person $person
     * @param CarbonInterface|null $cakeDay
     *
     * @return Day[]
     */
    public function addBirthDayToNextAvailableWorkDay(
        array $workDays,
        int $year,
        Person $person,
        CarbonInterface $cakeDay = null
    ): array {
        if ($cakeDay === null) {
            $cakeDay = $person->getBirthday($year)->copy()->addDay();
        }

        if (array_key_exists($cakeDay->dayOfYear -1, $workDays)) {
            $workDays[$cakeDay->dayOfYear - 1]->addPerson($person);

            return $workDays;
        }

        if ($cakeDay->year > $year) {
            // This person birthday will be celebrated next year so we wont assign it.
            return $workDays;
        }

        $cakeDay = $cakeDay->copy()->addDay();

        return $this->addBirthDayToNextAvailableWorkDay($workDays, $year, $person, $cakeDay);
    }

    private function removeNonWorkDays(Day ...$daysOfYear): array
    {
        return array_filter($daysOfYear, function (Day $day) {
            if (!in_array($day->getDate()->dayOfWeek, self::WEEKDAYS)) {
                return false;
            }

            $holidays = [
                Carbon::createFromFormat('Y-m-d', '2000-01-01'),
                Carbon::createFromFormat('Y-m-d', '2000-12-25'),
                Carbon::createFromFormat('Y-m-d', '2000-12-26'),
            ];

            foreach ($holidays as $holiday) {
                if ($day->getDate()->format('m-d') === $holiday->format('m-d')) {
                    return false;
                }
            }

            return true;
        });
    }

    private function reassignForHealthReasons(Day ...$cakeDaySchedule): array
    {
        $reassignedDates = [];

        foreach ($cakeDaySchedule as $dayOfYear => $day) {
            if (!$this->reschedulingRequired($dayOfYear, $day, $cakeDaySchedule, $reassignedDates)) {
                continue;
            }

            $nextDayIsCakeDay = $cakeDaySchedule[$dayOfYear + 1]->isCakeDay();
            $previousDayHasHadCakeDaysMovedToItForHealthReasons = in_array($dayOfYear - 1, $reassignedDates, true);

            if (!$previousDayHasHadCakeDaysMovedToItForHealthReasons && !$nextDayIsCakeDay) {
                // This should never happen
                throw new \OutOfBoundsException('Situation no accounted for.');
            }

            // Move birthdays to next day
            $cakeDaySchedule[$dayOfYear + 1]->addPeople(...$cakeDaySchedule[$dayOfYear]->getPeople());

            // Make None Cake Day
            $cakeDaySchedule[$dayOfYear]->removePeople();
            $reassignedDates[] = $dayOfYear + 1;
        }

        return $cakeDaySchedule;
    }

    private function getDaysForYear(CarbonInterface $startDate): array
    {
        $firstDayOfYear = $startDate->copy()->firstOfYear();
        $lastDayOfYear = $startDate->copy()->endOfYear();

        $daysForYear = CarbonInterval::day(1)->toPeriod($firstDayOfYear, $lastDayOfYear);

        $output = [];
        // We could pass around the Carbon Period Iterator but i think an array may keep things simpler
        foreach ($daysForYear as $date) {
            $output[$date->dayOfYear] = new Day($date);
        }

        return $output;
    }

    private function reschedulingRequired(
        int $dayOfYear,
        Day $day,
        array $cakeDaySchedule,
        array $reassignedDates
    ): bool {
        // Day is not a cake day
        if (!$day->isCakeDay()) {
            return false;
        }

        // Next day is non work day
        if (!array_key_exists($dayOfYear + 1, $cakeDaySchedule)) {
            return false;
        }

        $previousDayHasHadCakeDaysMovedToItForHealthReasons = in_array($dayOfYear - 1, $reassignedDates, true);

        // Next day is not a cake day and was not rescheduled
        if (!$previousDayHasHadCakeDaysMovedToItForHealthReasons && !$cakeDaySchedule[$dayOfYear + 1]->isCakeDay()) {
            return false;
        }

        // We've already moved the cake day due to 2 consecutive days, we can't move it again.
        if (in_array($dayOfYear, $reassignedDates, true)) {
            return false;
        }

        return true;
    }
}
