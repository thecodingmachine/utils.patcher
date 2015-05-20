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
 * Command to list all patches
 */
class ListPatchesCommand extends Command
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
        ->setName('patches:list')
        ->setDescription('List all the patches.')
        ->setDefinition(array(

        ))
        ->setHelp(<<<EOT
List all patches declared in Mouf patch service.

The command will display the status of each patch, i.e. whether it has been applied or skipped or is waiting to be applied.
EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $patches = $this->patchService->getView();

        $rows = array_map(function($row) {
            return [ $row['uniqueName'], $this->renderStatus($row['status']) ];
        }, $patches);

        $table = new Table($output);
        $table
            ->setHeaders(array('Patch', 'Status'))
            ->setRows($rows)
        ;
        $table->render();
    }

    private function renderStatus($status) {
        $map = [
            PatchInterface::STATUS_APPLIED => "<info>Applied</info>",
            PatchInterface::STATUS_SKIPPED => "<comment>Skipped</comment>",
            PatchInterface::STATUS_AWAITING => "Awaiting",
            PatchInterface::STATUS_ERROR => "<error>Skipped</error>",
        ];
        if (!isset($map[$status])) {
            throw new \Exception('Unexpected status "'.$map[$status].'"');
        }
        return $map[$status];
    }
}
