<?php


namespace Mouf\Utils\Patcher\Dumper;


use Symfony\Component\Console\Output\OutputInterface;

class Dumper implements DumperInterface
{
    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function dumpPatch(string $code): void
    {
        $this->output->writeln($code);
    }
}