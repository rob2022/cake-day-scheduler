<?php
declare(strict_types=1);

namespace CakeDay\Services\Writers;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface as ConsoleOutPutInterface;

class CakeDayConsoleWriter extends CakeDayAbstractWriter
{
    private ConsoleOutPutInterface $output;

    public function __construct(ConsoleOutPutInterface $output)
    {
        $this->output = $output;
    }

    public function writeRows(array $rows): void
    {
        $table = new Table($this->output);
        $table->setHeaders(array_shift($rows))
            ->setRows($rows);
        $table->render();
    }
}
