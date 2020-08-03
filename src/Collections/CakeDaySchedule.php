<?php
declare(strict_types=1);

namespace CakeDay\Collections;

use CakeDay\Entities\Day;
use Carbon\CarbonInterface;
use Doctrine\Common\Collections\ArrayCollection;

class CakeDaySchedule extends ArrayCollection
{
    /**
     * @param Day[] $elements
     */
    public function __construct(array $elements)
    {
        foreach ($elements as $element) {
            $this->assertValidCakeDay($element);
        }

        parent::__construct($elements);
    }

    private function assertValidCakeDay(Day $day): void
    {
        if (!$day->isCakeDay()) {
            throw new \InvalidArgumentException('Day must be a cake day.');
        }
    }

    public function getByDate(CarbonInterface $date): Day
    {
        $date = $date->copy()->startOfDay();
        /** @var Day $day */
        foreach ($this as $day) {
            if ($day->getDate()->equalTo($date)) {
                return $day;
            }
        }

        throw new \OutOfBoundsException('No Day for given date ' . $date->format('Y-m-d'));
    }
}
