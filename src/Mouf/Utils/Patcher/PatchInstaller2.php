<?php
/*
 * Copyright (c) 2013-2015 David Negrier
 *
 * See the file LICENSE.txt for copying permission.
 */
namespace Mouf\Utils\Patcher;

use Mouf\Actions\InstallUtils;
use Mouf\Console\ConsoleUtils;
use Mouf\Database\Patcher\PatchConnection;
use Mouf\Installer\PackageInstallerInterface;
use Mouf\MoufManager;
use Mouf\Utils\Patcher\Commands\ApplyAllPatchesCommand;
use Mouf\Utils\Patcher\Commands\ApplyPatchCommand;
use Mouf\Utils\Patcher\Commands\ListPatchesCommand;
use Mouf\Utils\Patcher\Commands\RevertPatchCommand;
use Mouf\Utils\Patcher\Commands\SkipPatchCommand;

class PatchInstaller2 implements PackageInstallerInterface
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
        $patchDefaultType = InstallUtils::getOrCreateInstance('patch.default_type', PatchType::class, $moufManager);
        $patchDefaultType->getConstructorArgumentProperty('name')->setValue('');
        $patchDefaultType->getConstructorArgumentProperty('description')->setValue('Patches that should be always applied should have this type. Typically, use this type for DDL changes or reference data insertion.');

        $patchTestDataType = InstallUtils::getOrCreateInstance('patch.testdata_type', PatchType::class, $moufManager);
        $patchTestDataType->getConstructorArgumentProperty('name')->setValue('test_data');
        $patchTestDataType->getConstructorArgumentProperty('description')->setValue('Use this type to mark patches that contain test data that should only be used in staging environment.');

        $patchService = InstallUtils::getOrCreateInstance('patchService', PatchService::class, $moufManager);

        if (empty($patchService->getConstructorArgumentProperty('types')->getValue())) {
            $patchService->getConstructorArgumentProperty('types')->setValue([ $patchDefaultType, $patchTestDataType ]);
        }

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
