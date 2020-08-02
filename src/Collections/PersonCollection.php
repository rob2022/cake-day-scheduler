<?php
declare(strict_types=1);

namespace CakeDay\Collections;

use CakeDay\Entities\Person;
use Doctrine\Common\Collections\ArrayCollection;

class PersonCollection extends ArrayCollection
{
    public function __construct(array $people = [])
    {
        $people = $this->sortPeopleByCakeDay(...$people);

        parent::__construct($people);
    }

    private function sortPeopleByCakeDay(Person ...$people): array
    {
        usort($people, function (Person $a, Person $b) {
            return $a->getCakeDay(2000)->timestamp <=> $b->getCakeDay(2000)->timestamp;
        });

        return $people;
    }
}
