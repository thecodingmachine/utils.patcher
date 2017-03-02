<?php
namespace Mouf\Utils\Patcher\Controllers;

use Mouf\Controllers\AbstractMoufInstanceController;

use Mouf\Database\TDBM\Utils\TDBMDaoGenerator;

use Mouf\Html\Widgets\MessageService\Service\UserMessageInterface;
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

	protected $nbPatchesByType = [];
	
	/**
	 * Page listing the patches to be applied.
	 *
	 * @Action
	 * @Logged
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
		
		$this->content->addFile(__DIR__."/../../../../views/patchesList.php", $this);
		$this->template->toHtml();
	}
	

	/**
	 * Runs a patch.
	 * 
	 * @Action
	 * @Logged
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
     * Displays the page to select the patch types to be applied.
     *
     * @Action
     * @Logged
     * @param string $name
     * @param string $selfedit
     */
    public function runAllPatches($name, $selfedit) {
        $this->initController($name, $selfedit);

        $patchService = new InstanceProxy($name, $selfedit == "true");
        $this->patchesArray = $patchService->getView();

        $types = $patchService->_getSerializedTypes();

        foreach ($types as $type) {
            $this->nbPatchesByType[$type['name']] = 0;
        }

        $nbNoneDefaultPatches = 0;

        foreach ($this->patchesArray as $patch) {
            if ($patch['status'] == PatchInterface::STATUS_AWAITING || $patch['status'] == PatchInterface::STATUS_ERROR) {
                $type = $patch['patch_type'];
                if ($type !== '') {
                    $nbNoneDefaultPatches++;
                }
                $this->nbPatchesByType[$type]++;
            }
        }

        // If all patches to be applied are default patches, let's do this right now.
        if ($nbNoneDefaultPatches === 0) {
            $this->applyAllPatches($name, [''], $selfedit);
            return;
        }

        ksort($this->nbPatchesByType);

        // Otherwise, let's display a screen to select the patch types to be applied.
        $this->content->addFile(__DIR__."/../../../../views/applyPatches.php", $this);
        $this->template->toHtml();
    }


    /**
     * Runs all patches in a row.
     *
     * @Action
     * @Logged
     * @param string $name
     * @param array $types
     * @param string $selfedit
     */
	public function applyAllPatches($name, array $types, $selfedit) {
		$patchService = new InstanceProxy($name, $selfedit == "true");
		$this->patchesArray = $patchService->getView();

		// Array of cound of applied and skip patched. Key is the patch type.
		$appliedPatchArray = [];
        $skippedPatchArray = [];

		try {
			foreach ($this->patchesArray as $patch) {
                if ($patch['status'] == PatchInterface::STATUS_AWAITING || $patch['status'] == PatchInterface::STATUS_ERROR) {
                    $type = $patch['patch_type'];
                    if (in_array($type, $types) || $type === '') {
                        $patchService->apply($patch['uniqueName']);
                        if (!isset($appliedPatchArray[$type])) {
                            $appliedPatchArray[$type] = 0;
                        }
                        $appliedPatchArray[$type]++;
                    } else {
                        $patchService->skip($patch['uniqueName']);
                        if (!isset($skippedPatchArray[$type])) {
                            $skippedPatchArray[$type] = 0;
                        }
                        $skippedPatchArray[$type]++;
                    }
                }
			}

		} catch (\Exception $e) {
			$htmlMessage = "An error occured while applying the patch: ".$e->getMessage();
			set_user_message($htmlMessage);
		}

        $this->displayNotificationMessage($appliedPatchArray, $skippedPatchArray);

        header('Location: .?name='.urlencode($name));
	}

	private function displayNotificationMessage(array $appliedPatchArray, array $skippedPatchArray)
    {
        $nbPatchesApplied = array_sum($appliedPatchArray);
        $nbPatchesSkipped = array_sum($skippedPatchArray);
        $msg = '';
        if ($nbPatchesApplied !== 0) {
            $patchArr = [];
            foreach ($appliedPatchArray as $name => $number) {
                $name = $name ?: 'default';
                $patchArr[] = plainstring_to_htmlprotected($name).': '.$number;
            }

            $msg .= sprintf('%d patch(es) applied (%s)', $nbPatchesApplied, implode(', ', $patchArr));
        }
        if ($nbPatchesSkipped !== 0) {
            $patchArr = [];
            foreach ($skippedPatchArray as $name => $number) {
                $name = $name ?: 'default';
                $patchArr[] = plainstring_to_htmlprotected($name).': '.$number;
            }

            $msg .= sprintf('%d patch(es) skipped (%s)', $nbPatchesSkipped, implode(', ', $patchArr));
        }

        if ($msg !== '') {
            set_user_message($msg, UserMessageInterface::SUCCESS);
        }
    }
}