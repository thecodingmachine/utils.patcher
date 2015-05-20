<?php
namespace Mouf\Utils\Patcher\Commands;

use Mouf\Utils\Patcher\PatchInterface;
use Mouf\Utils\Patcher\PatchService;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Command to skip a given patch
 */
class SkipPatchCommand extends Command
{
    /**
     * @var PatchService
     */
    private $patchService;

    public function __construct(PatchService $patchService)
    {
        parent::__construct();
        $this->patchService = $patchService;
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('patches:skip')
        ->setDescription('Skip a patch.')
        ->addArgument(
            'name',
            InputArgument::REQUIRED,
            'The name of the patch instance to be skipped'
        )
        ->setHelp(<<<EOT
Skips a patch. You must pass in parameter the name of the patch.
EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $patchName = $input->getArgument('name');
        $this->patchService->skip($patchName);

        $output->writeln('Patch marked as skipped');
    }
}
