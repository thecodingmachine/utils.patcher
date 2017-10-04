<?php
namespace Mouf\Utils\Patcher\Commands;

use Mouf\Utils\Patcher\Dumper\Dumper;
use Mouf\Utils\Patcher\PatchInterface;
use Mouf\Utils\Patcher\PatchService;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Command to reset and reapply all patches
 */
class ResetPatchesCommand extends AbstractApplyAllCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('patches:reset')
        ->setDescription('Reset database and reapply all patches.')
        ->setDefinition(array(

        ))
        ->setHelp(<<<EOT
Reset the database and reapplies all pending patches. You can select the type of patches to be applied using the options. Default patches are always applied.

Use patches:apply-all if you want to apply remaining patches without resetting the database.
EOT
        );

        $this->registerOptions();

        $this->addOption('dump', 'd', InputOption::VALUE_NONE, 'Dumps the patch to the output. Note: this is not a "dry" mode. The database will still be reset.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('dump')) {
            $this->patchService->setDumper(new Dumper($output));
        }

        $this->patchService->reset();

        $this->applyAll($input, $output);
    }
}
