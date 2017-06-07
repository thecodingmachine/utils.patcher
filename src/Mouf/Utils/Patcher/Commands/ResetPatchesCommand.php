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
 * Command to reset and reapply all patches
 */
class ResetPatchesCommand extends Command
{
    /**
     * @var PatchService
     */
    private $patchService;

    public function __construct(PatchService $patchService)
    {
        $this->patchService = $patchService;
        parent::__construct();
    }


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

        foreach ($this->patchService->getTypes() as $type) {
            if ($type->getName() !== '') {
                $this->addOption($type->getName(), null, InputOption::VALUE_NONE, 'Applies patches of type "'.$type->getName().'". '.$type->getDescription());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->patchService->reset();

        $patchesArray = $this->patchService->getView();

        $count = 0;
        try {
            foreach ($patchesArray as $patch) {
                $this->patchService->apply($patch['uniqueName']);
                $count++;
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf(
                    'An error occurred while applying patch <info>%s</info>: <error>%s</error>', $patch['uniqueName'], $e->getMessage()
                ));
            throw $e;
        }

        if ($count) {
            $output->writeln(sprintf(
                    'Database has been reset, <info>%d</info> patches successfully applied', $count
                ));
        } else {
            $output->writeln('<info>Database has been reset, no patches to apply</info>');
        }
    }
}
