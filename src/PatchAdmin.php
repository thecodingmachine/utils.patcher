<?php
use Mouf\MoufManager;
use Mouf\MoufUtils;

MoufUtils::registerMainMenu('utilsMainMenu', 'Utils', null, 'mainMenu', 200);
MoufUtils::registerMenuItem('utilsPatchInterfaceMenu', 'Patches management', null, 'utilsMainMenu', 50);
MoufUtils::registerChooseInstanceMenuItem('utilsPatchListInterfaceMenuItem', 'View patches list', 'patcher/', 'Mouf\\Utils\\Patcher\\PatchService', 'utilsPatchInterfaceMenu', 10);

// Controller declaration
$moufManager = MoufManager::getMoufManager();
$moufManager->declareComponent('patcher', 'Mouf\\Utils\\Patcher\\Controllers\\PatchController', true);
$moufManager->bindComponents('patcher', 'template', 'moufTemplate');
$moufManager->bindComponents('patcher', 'content', 'block.content');

