<?php


namespace Mouf\Utils\Patcher\Commands;


use Mouf\Utils\Patcher\PatchService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractApplyAllCommand extends Command
{
    /**
     * @var PatchService
     */
    protected $patchService;

    public function __construct(PatchService $patchService)
    {
        $this->patchService = $patchService;
        parent::__construct();
    }

    protected function registerOptions(): void
    {
        foreach ($this->patchService->getTypes() as $type) {
            if ($type->getName() !== '') {
                $this->addOption($type->getName(), null, InputOption::VALUE_NONE, 'Applies patches of type "'.$type->getName().'". '.$type->getDescription());
            }
        }
    }

    protected function applyAll(InputInterface $input, OutputInterface $output)
    {
        $types = [];
        foreach ($this->patchService->getTypes() as $type) {
            if ($type->getName() !== '' && $input->getOption($type->getName())) {
                $types[] = $type->getName();
            }
        }

        try {

            [
                'applied' => $appliedPatchArray,
                'skipped' => $skippedPatchArray
            ] = $this->patchService->applyAll($types);

        } catch (\Exception $e) {
            $output->writeln(sprintf(
                'An error occurred while applying patch: <error>%s</error>', $e->getMessage()
            ));
            throw $e;
        }

        $msg = $this->getNotificationMessage($appliedPatchArray, $skippedPatchArray);
        if ($msg) {
            $output->writeln($msg);
        } else {
            $output->writeln('<info>No patches to apply</info>');
        }
    }

    private function getNotificationMessage(array $appliedPatchArray, array $skippedPatchArray): string
    {
        $nbPatchesApplied = array_sum($appliedPatchArray);
        $nbPatchesSkipped = array_sum($skippedPatchArray);
        $msg = '';
        if ($nbPatchesApplied !== 0) {
            $patchArr = [];
            foreach ($appliedPatchArray as $name => $number) {
                $name = $name ?: 'default';
                $patchArr[] = $name.': <info>'.$number.'</info>';
            }

            $msg .= sprintf('<info>%d</info> patch%s applied (%s)', $nbPatchesApplied, ($nbPatchesApplied > 1)?'es':'', implode(', ', $patchArr))."\n";
        }
        if ($nbPatchesSkipped !== 0) {
            $patchArr = [];
            foreach ($skippedPatchArray as $name => $number) {
                $name = $name ?: 'default';
                $patchArr[] = $name.': <info>'.$number.'</info>';
            }

            $msg .= sprintf('<info>%d</info><comment> patch%s skipped</comment> (%s)', $nbPatchesSkipped, ($nbPatchesSkipped > 1)?'es':'', implode(', ', $patchArr));
        }

        return $msg;
    }
}