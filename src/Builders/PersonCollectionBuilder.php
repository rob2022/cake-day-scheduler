<?php
declare(strict_types=1);

namespace CakeDay\Builders;

use CakeDay\Collections\PersonCollection;
use CakeDay\Entities\Person;
use Carbon\Carbon;
use SplFileObject;

class PersonCollectionBuilder
{
    public function buildFromFile(SplFileObject $file): PersonCollection
    {
        $people = [];
        while (!$file->eof()) {
            $string = $file->fgets();

            // Ignore blank lines
            if (!trim($string)) {
                continue;
            }

            [$name, $dateOfBirth] = explode(',', $string);

            $people[] = new Person($name, Carbon::createFromFormat('Y-m-d', trim($dateOfBirth)));
        }

        return new PersonCollection($people);
    }
}
