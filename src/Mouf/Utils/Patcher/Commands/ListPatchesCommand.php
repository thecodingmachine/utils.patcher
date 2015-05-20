<?php
namespace Mouf\Utils\Patcher\Commands;

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
        $this->patchService = $patchService;
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('patches:list')
        ->setAliases(array('patch:list'))
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

        /*$patchView = array(
            "uniqueName"=>$uniqueName,
            "status"=>$status,
            "canRevert"=>$canRevert,
            "description"=>$description,
            "error_message"=>$error_message,
            "edit_url"=>$editUrl
        );*/

        $rows = array_map(function($row) {
            return [ $row['uniqueName'], $row['status'] ];
        }, $patches);

        $table = new Table($output);
        $table
            ->setHeaders(array('Patch', 'Status'))
            ->setRows($rows)
        ;
        $table->render();


        /*$output->writeln(PHP_EOL . sprintf(
                'Exporting "<info>%s</info>" mapping information to "<info>%s</info>"', $toType, $destPath
            ));*/
    }
}
