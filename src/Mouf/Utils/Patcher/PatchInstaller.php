<?php
/*
 * Copyright (c) 2013-2015 David Negrier
 *
 * See the file LICENSE.txt for copying permission.
 */
namespace Mouf\Utils\Patcher;

use Mouf\Actions\InstallUtils;
use Mouf\Console\ConsoleUtils;
use Mouf\Installer\PackageInstallerInterface;
use Mouf\MoufManager;
use Mouf\Utils\Patcher\Commands\ApplyAllPatchesCommand;
use Mouf\Utils\Patcher\Commands\ApplyPatchCommand;
use Mouf\Utils\Patcher\Commands\ListPatchesCommand;
use Mouf\Utils\Patcher\Commands\RevertPatchCommand;
use Mouf\Utils\Patcher\Commands\SkipPatchCommand;

class PatchInstaller implements PackageInstallerInterface
{
    /**
     * (non-PHPdoc)
     * @see \Mouf\Installer\PackageInstallerInterface::install()
     * @param  MoufManager         $moufManager
     * @throws \Mouf\MoufException
     */
    public static function install(MoufManager $moufManager)
    {
        // Let's create the instance.
        $patchService = InstallUtils::getOrCreateInstance('patchService', 'Mouf\\Utils\\Patcher\\PatchService', $moufManager);

        $consoleUtils = new ConsoleUtils($moufManager);

        $listPatchesCommand = $moufManager->createInstance(ListPatchesCommand::class);
        $listPatchesCommand->getConstructorArgumentProperty("patchService")->setValue($patchService);
        $consoleUtils->registerCommand($listPatchesCommand);

        $applyAllPatchesCommand = $moufManager->createInstance(ApplyAllPatchesCommand::class);
        $applyAllPatchesCommand->getConstructorArgumentProperty("patchService")->setValue($patchService);
        $consoleUtils->registerCommand($applyAllPatchesCommand);

        $applyPatchCommand = $moufManager->createInstance(ApplyPatchCommand::class);
        $applyPatchCommand->getConstructorArgumentProperty("patchService")->setValue($patchService);
        $consoleUtils->registerCommand($applyPatchCommand);

        $skipPatchCommand = $moufManager->createInstance(SkipPatchCommand::class);
        $skipPatchCommand->getConstructorArgumentProperty("patchService")->setValue($patchService);
        $consoleUtils->registerCommand($skipPatchCommand);

        $revertPatchCommand = $moufManager->createInstance(RevertPatchCommand::class);
        $revertPatchCommand->getConstructorArgumentProperty("patchService")->setValue($patchService);
        $consoleUtils->registerCommand($revertPatchCommand);


        // Let's rewrite the MoufComponents.php file to save the component
        $moufManager->rewriteMouf();
    }
}
