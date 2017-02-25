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
 * Command to apply all patches
 */
class ApplyAllPatchesCommand extends Command
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
        ->setName('patches:apply-all')
        ->setDescription('Apply pending patches.')
        ->setDefinition(array(

        ))
        ->setHelp(<<<EOT
Apply pending patches. You can select the type of patches to be applied using the options. Default patches are always applied.

Use patches:apply if you want to cherry-pick a particular patch.
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
        $patchesArray = $this->patchService->getView();

        $count = 0;
        try {
            foreach ($patchesArray as $patch) {
                if ($patch['status'] == PatchInterface::STATUS_AWAITING || $patch['status'] == PatchInterface::STATUS_ERROR) {
                    $this->patchService->apply($patch['uniqueName']);
                    $count++;
                }
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf(
                    'An error occured while applying patch <info>%s</info>: <error>%s</error>', $patch['uniqueName'], $e->getMessage()
                ));
            throw $e;
        }

        if ($count) {
            $output->writeln(sprintf(
                    '<info>%d</info> patches successfully applied', $count
                ));
        } else {
            $output->writeln('<info>No patches to apply</info>');
        }
    }
}
