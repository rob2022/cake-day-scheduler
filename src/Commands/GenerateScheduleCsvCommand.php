<?php
declare(strict_types=1);

namespace CakeDay\Commands;

use CakeDay\Builders\CakeDayScheduleBuilder;
use CakeDay\Builders\PersonCollectionBuilder;
use CakeDay\Collections\CakeDaySchedule;
use CakeDay\Collections\PersonCollection;
use CakeDay\Services\Writers\CakeDayConsoleWriter;
use CakeDay\Services\Writers\CakeDayCsvWriter;
use Carbon\Carbon;
use Exception;
use SplFileObject;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as ConsoleInputInterface;
use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Throwable;

class GenerateScheduleCsvCommand extends SingleCommandApplication
{
    protected static $defaultName = 'app:generate-cake-day-schedule';

    protected function configure(): void
    {
        $this->addArgument('input-file', InputArgument::REQUIRED, 'The text file to be parsed.')
            ->addArgument('output-file', InputArgument::REQUIRED, 'The output file.');
    }

    public function execute(ConsoleInputInterface $input, ConsoleOutputInterface $output): int
    {
        $inputFilePath = $input->getArgument('input-file');
        $outputFilePath = $input->getArgument('output-file');

        $people = $this->getPeople($this->getInputFile($inputFilePath));
        $cakeDaySchedule = $this->getCakeDaySchedule($people);

        $this->writeCsv($outputFilePath, $cakeDaySchedule);
        $this->writeToConsole($output, $cakeDaySchedule, $outputFilePath);

        return 0;
    }

    private function getPeople(SplFileObject $inputFile): PersonCollection
    {
        $personCollectionBuilder = new PersonCollectionBuilder();

        return $personCollectionBuilder->buildFromFile($inputFile);
    }

    private function getCakeDaySchedule(PersonCollection $people): CakeDaySchedule
    {
        $cakeDayScheduleBuilder = new CakeDayScheduleBuilder();

        return  $cakeDayScheduleBuilder->build(Carbon::now(), $people);
    }

    private function getInputFile($inputFilePath): SplFileObject
    {
        try {
            $inputFile = new SplFileObject($inputFilePath);
        } catch (Throwable $exception) {
            throw new Exception('Unable to open ' . $inputFilePath);
        }

        return $inputFile;
    }

    private function writeCsv($outputFilePath, CakeDaySchedule $cakeDaySchedule): void
    {
        try {
            $csvWriter = new CakeDayCsvWriter($outputFilePath);
            $csvWriter->write($cakeDaySchedule);
        } catch (Throwable $exception) {
            throw new Exception('Unable to write to  ' . $outputFilePath);
        }
    }

    private function writeToConsole(
        ConsoleOutputInterface $output,
        CakeDaySchedule $cakeDaySchedule,
        string $outputFilePath
    ): void {
        $consoleWriter = new CakeDayConsoleWriter($output);
        $consoleWriter->write($cakeDaySchedule);

        $output->writeln('<info>Success: Exported cake day schedule to ' . $outputFilePath . '</info>');
    }
}
