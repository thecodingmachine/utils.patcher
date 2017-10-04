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
 * Command to apply all patches
 */
class ApplyAllPatchesCommand extends AbstractApplyAllCommand
{


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('patches:apply-all')
        ->setDescription('Apply pending patches.')
        ->setDefinition(array(

        ))
        ->setHelp(<<<EOT
Apply pending patches. You can select the type of patches to be applied using the options. Default patches are always applied.

Use patches:apply if you want to cherry-pick a particular patch.
EOT
        );

        $this->registerOptions();

        $this->addOption('dump', 'd', InputOption::VALUE_NONE, 'Dumps the patches to the output. Note: this is not a "dry" mode. The patches will still be applied.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('dump')) {
            $this->patchService->setDumper(new Dumper($output));
        }
        $this->applyAll($input, $output);
    }
}
