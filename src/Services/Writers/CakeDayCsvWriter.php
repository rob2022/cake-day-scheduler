<?php
declare(strict_types=1);

namespace CakeDay\Services\Writers;

use CakeDay\Collections\CakeDaySchedule;
use CakeDay\Entities\Day;
use CakeDay\Entities\Person;
use League\Csv\EscapeFormula;
use League\Csv\Writer;

class CakeDayCsvWriter extends CakeDayAbstractWriter
{
    private string $outPutPath;

    public function __construct(string $outPutPath)
    {
        $this->outPutPath = $outPutPath;
    }
    protected function writeRows(array $rows): void
    {
        $writer = Writer::createFromPath($this->outPutPath, 'w+');
        $writer->addFormatter(new EscapeFormula());
        $writer->insertAll($rows);
    }
}
