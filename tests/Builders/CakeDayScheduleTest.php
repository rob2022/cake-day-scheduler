<?php
declare(strict_types=1);

namespace CakeDay\Builders;
use CakeDay\Collections\CakeDaySchedule;
use CakeDay\Collections\PersonCollection;
use CakeDay\Entities\Person;
use CakeDay\TestCase;
use Carbon\Carbon;

class CakeDayScheduleTest extends TestCase
{
    /**
     * @dataProvider birthDayToCakeDayMapProvider
     */
    public function testConstruction(...$cakeDayAssertionData)
    {
        $builder = new CakeDayScheduleBuilder();

        $people = array_map(function (array $cakeDayData) {
            return new Person(
                $cakeDayData['name'],
                Carbon::createFromFormat('Y-m-d', $cakeDayData['dateOfBirth'])
            );
        }, $cakeDayAssertionData);

        $personCollection = new PersonCollection($people);

        $cakeDayCollection = $builder->build(Carbon::now()->firstOfYear(), $personCollection);

        $this->assertInstanceOf(CakeDaySchedule::class, $cakeDayCollection);

        foreach ($cakeDayAssertionData as $cakeDayDatum) {
            $cakeDay = $cakeDayCollection->getByDate(Carbon::createFromFormat('Y-m-d', $cakeDayDatum['cakeDay']));
            $this->assertContains(
                $cakeDayDatum['name'],
                $this->mapPeopleArrayToNameArray(...$cakeDay->getPeople())
            );
        }
    }

    public function mapPeopleArrayToNameArray(Person ...$people)
    {
        return array_map(fn(Person $person) => $person->getname(), $people);
    }

    public function birthDayToCakeDayMapProvider()
    {
        return [
            [ // assigns to next day when no issues
                ['name' => $this->randomName(), 'dateOfBirth' => '1985-01-02', 'cakeDay' => '2020-01-03'],
            ],
            [ // correctly skips weekend
                ['name' => $this->randomName(), 'dateOfBirth' => '1985-01-03', 'cakeDay' => '2020-01-06'],
            ],
            [ // correctly skips holidays
                ['name' => $this->randomName(), 'dateOfBirth' => '1985-12-24', 'cakeDay' => '2020-12-28'],
            ],
            [ // reassigned for health reasons
                ['name' => $this->randomName(), 'dateOfBirth' => '1985-1-05', 'cakeDay' => '2020-1-07'],
                ['name' => $this->randomName(), 'dateOfBirth' => '1985-1-06', 'cakeDay' => '2020-1-07'],
                ['name' => $this->randomName(), 'dateOfBirth' => '1985-1-07', 'cakeDay' => '2020-1-09'],
                ['name' => $this->randomName(), 'dateOfBirth' => '1985-1-08', 'cakeDay' => '2020-1-09'],
                ['name' => $this->randomName(), 'dateOfBirth' => '1985-1-09', 'cakeDay' => '2020-1-13'],
            ],
        ];
    }

    public function randomName(): string
    {
        return uniqid('name_', true);
    }
}
