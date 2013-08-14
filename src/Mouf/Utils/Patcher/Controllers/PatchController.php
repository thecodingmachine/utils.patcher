<?php
namespace Mouf\Utils\Patcher\Controllers;

use Mouf\Controllers\AbstractMoufInstanceController;

use Mouf\Database\TDBM\Utils\TDBMDaoGenerator;

use Mouf\MoufManager;

use Mouf\Mvc\Splash\Controllers\Controller;

use Mouf\Reflection\MoufReflectionProxy;

use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\InstanceProxy;
use Mouf\Utils\Patcher\PatchException;
use Mouf\Utils\Patcher\PatchInterface;

/**
 * The controller to track which patchs have been applied.

 */
class PatchController extends AbstractMoufInstanceController {
	
	/**
	 *
	 * @var HtmlBlock
	 */
	public $content;
	
	/**
	 * A list of patches returned by the getView method of the PatchService. 
	 * @var array
	 */
	protected $patchesArray;
	
	protected $nbAwaiting = 0;
	protected $nbError = 0;
	
	/**
	 * Page listing the patches to be applied.
	 *
	 * @Action
	 */
	public function defaultAction($name, $selfedit="false") {
		$this->initController($name, $selfedit);
		
		$patchService = new InstanceProxy($name, $selfedit == "true");
		$this->patchesArray = $patchService->getView();
		
		foreach ($this->patchesArray as $patch) {
			if ($patch['status'] == PatchInterface::STATUS_AWAITING) {
				$this->nbAwaiting++;
			} elseif ($patch['status'] == PatchInterface::STATUS_ERROR) {
				$this->nbError++;
			}
		}
		
		$this->content->addFile(dirname(__FILE__)."/../../../../views/patchesList.php", $this);
		$this->template->toHtml();
	}
	

	/**
	 * Runs a patch.
	 * 
	 * @Action
	 * @param string $name
	 * @param string $uniqueName
	 * @param string $action
	 */
	public function runPatch($name, $uniqueName, $action, $selfedit) {
		
		$patchService = new InstanceProxy($name, $selfedit == "true");
		
		try {
		
			if ($action == 'apply') {
				$this->patchesArray = $patchService->apply($uniqueName);
			} else if ($action == 'revert') {
				$this->patchesArray = $patchService->revert($uniqueName);
			} else if ($action == 'skip') {
				$this->patchesArray = $patchService->skip($uniqueName);
			} else {
				throw new PatchException("Unknown action: '".$action."'");
			}
		} catch (\Exception $e) {
			$htmlMessage = "An error occured while applying the patch: ".$e->getMessage();
			set_user_message($htmlMessage);
		}

		header('Location: .?name='.urlencode($name));
	}
	
	/**
	 * Runs all patches in a row.
	 *
	 * @Action
	 * @param string $name
	 * @param string $selfedit
	 */
	public function runAllPatches($name, $selfedit) {
		$patchService = new InstanceProxy($name, $selfedit == "true");
		$this->patchesArray = $patchService->getView();
		
		try {
			foreach ($this->patchesArray as $patch) {
				if ($patch['status'] == PatchInterface::STATUS_AWAITING || $patch['status'] == PatchInterface::STATUS_ERROR) {
					$patchService->apply($patch['uniqueName']);
				}
			}
		} catch (\Exception $e) {
			$htmlMessage = "An error occured while applying the patch: ".$e->getMessage();
			set_user_message($htmlMessage);
		}
		
		header('Location: .?name='.urlencode($name));
	}
}