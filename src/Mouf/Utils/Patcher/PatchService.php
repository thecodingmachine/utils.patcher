<?php
/*
 Copyright (C) 2013-2017 David Négrier - THE CODING MACHINE

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace Mouf\Utils\Patcher;

use Mouf\Validator\MoufValidatorInterface;
use Mouf\MoufManager;
use Mouf\Validator\MoufValidatorResult;
/**
 * The patch service is in charge of applying a list of patches attached to this application.
 * Especially, it contains the list of patch that has ever been declared.
 * 
 * @author David Negrier <david@mouf-php.com>
 * @ExtendedAction {"name":"View patches list", "url":"patcher/", "default":false}
 */
class PatchService implements MoufValidatorInterface {
    const IFEXISTS_EXCEPTION = "exception";
    const IFEXISTS_IGNORE = "ignore";


    /**
	 * The list of patches declared for this application.
	 * 
	 * @var PatchInterface[]
	 */
	private $patchs = [];

    /**
     * The list of exiting patch types for this application.
     *
     * @var PatchType[]
     */
	private $types = [];

    /**
     * @param PatchType[] $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
	 * The list of patches declared for this application.
	 * @param PatchInterface[] $patchs
	 * @return PatchService
	 */
	public function setPatchs(array $patchs) {
		$this->patchs = $patchs;
		return $this;
	}

    /**
     * The list of exiting patch types for this application.
     *
     * @return PatchType[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @internal Returns a serialized list of types for the patch UI.
     * @return array
     */
    public function _getSerializedTypes(): array
    {
        return array_map(function(PatchType $type) {
            return $type->jsonSerialize();
        }, $this->types);
    }

	/**
	 * Adds this patch to the list of existing patches.
	 * If the patch already exists, an exception is thrown.
	 * Patches are identified by their unique name.
	 * 
	 * 
	 * @param PatchInterface $patch
	 * @param string $ifExists
	 * @throws PatchException
	 * @return \Mouf\Utils\Patcher\PatchService
	 */
	public function register(PatchInterface $patch, $ifExists = self::IFEXISTS_IGNORE) {
		if ($this->has($patch->getUniqueName())) {
			if ($ifExists === self::IFEXISTS_IGNORE) {
				return $this;
			} else {
				throw new PatchException("The patch '".$patch->getUniqueName()."' is already registered.");
			}
		}
		$this->patchs[] = $patch;
		return $this;
	}
	
	/**
	 * Returns true if the patch whose name is $uniqueName is declared in this service.
	 * @param string $uniqueName
	 * @return boolean
	 */
	public function has($uniqueName) {
		foreach ($this->patchs as $patch) {
			if ($patch->getUniqueName() === $uniqueName) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns the patch whose name is $uniqueName.
	 * Throws an exception if the patch does not exists.
	 * @param string $uniqueName
	 * @return PatchInterface
	 */
	public function get($uniqueName) {
		foreach ($this->patchs as $patch) {
			if ($patch->getUniqueName() === $uniqueName) {
				return $patch;
			}
		}
		throw new PatchException("Unable to find patch whose unique name is '".$uniqueName."'");
	}
	
	/**
	 * Returns the number of patches that needs to be applied.
	 * 
	 * @return int
	 */
	public function getNbAwaitingPatchs(): int {
		$cnt = 0;
		foreach ($this->patchs as $patch) {
			if ($patch->getStatus() === PatchInterface::STATUS_AWAITING) {
				$cnt++;
			}
		}
		return $cnt;
	}
	
	/**
	 * Returns the number of patches that have errors.
	 *
	 * @return int
	 */
	public function getNbPatchsInError(): int {
		$cnt = 0;
		foreach ($this->patchs as $patch) {
			if ($patch->getStatus() === PatchInterface::STATUS_ERROR) {
				$cnt++;
			}
		}
		return $cnt;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\Validator\MoufValidatorInterface::validateInstance()
	 */
	public function validateInstance() {
		
		$nbPatchs = count($this->patchs);
		$nbAwaitingPatchs = $this->getNbAwaitingPatchs();
		$nbPatchesInError = $this->getNbPatchsInError();
		$instanceName = MoufManager::getMoufManager()->findInstanceName($this);
		
		if ($nbAwaitingPatchs === 0 && $nbPatchesInError === 0) {
			if ($nbPatchs === 0) {
				return new MoufValidatorResult(MoufValidatorResult::SUCCESS, "<strong>Patcher</strong>: No patches declared");
			} elseif ($nbPatchs == 0) {
				return new MoufValidatorResult(MoufValidatorResult::SUCCESS, "<strong>Patcher</strong>: The patch has been successfully applied");
			} else {
				return new MoufValidatorResult(MoufValidatorResult::SUCCESS, "<strong>Patcher</strong>: All $nbPatchs patches have been successfully applied");
			}
		} else {
			if ($nbPatchesInError == 0) {
				$status = MoufValidatorResult::WARN;
			} else {
				$status = MoufValidatorResult::ERROR;
			}
			
			$html = '<strong>Patcher</strong>: <a href="'.ROOT_URL.'vendor/mouf/mouf/patcher/?name='.$instanceName.'" class="btn btn-large btn-success patch-run-all"><i class="icon-arrow-right icon-white"></i> Apply ';
			if ($nbAwaitingPatchs != 0) {
				$html .= $nbAwaitingPatchs." awaiting patch".(($nbAwaitingPatchs != 1)?"es":"");
				if ($nbPatchesInError != 0) {
					$html .=" and";
				}
			}
			if ($nbPatchesInError != 0) {
				$html .=$nbPatchesInError." patch".(($nbPatchesInError != 1)?"es":"")." in error";
			}
			$html .='</a>';
				
			
			return new MoufValidatorResult($status, $html);
		}	
	}
	
	/**
	 * Returns a PHP array representing the patchs.
     *
     * @internal
	 */
	public function getView(): array {
		$view = array();
		foreach ($this->patchs as $patch) {
			$uniqueName = null;
			$status = null;
			$canRevert = null;
			$description = null;
			$error_message = null;
			$editUrl = null;
			$patchType = null;
			
			try {
				$uniqueName = $patch->getUniqueName();
				$canRevert = $patch->canRevert();
				$description = $patch->getDescription();
				$editUrl = $patch->getEditUrl()."&name=".MoufManager::getMoufManager()->findInstanceName($this);
				$status = $patch->getStatus();
				$error_message = $patch->getLastErrorMessage();
				$patchType = $patch->getPatchType()->getName();
				
			} catch (\Exception $e) {
				$status = PatchInterface::STATUS_ERROR;
				$error_message = $e->getMessage();
			}
			
			$patchView = array(
				"uniqueName"=>$uniqueName,
				"status"=>$status,
				"canRevert"=>$canRevert,
				"description"=>$description,
				"error_message"=>$error_message,
				"edit_url"=>$editUrl,
                "patch_type"=>$patchType
			);
			$view[] = $patchView;
		}
		return $view;
	}
	
	/**
	 * Applies the patch whose unique name is passed in parameter.
	 * @param string $uniqueName
	 */
	public function apply($uniqueName): void {
		$patch = $this->get($uniqueName);
		$patch->apply();
	}
	
	/**
	 * Skips the patch whose unique name is passed in parameter.
	 * @param string $uniqueName
	 */
	public function skip($uniqueName): void {
		$patch = $this->get($uniqueName);
		$patch->skip();
	}
	
	
	/**
	 * Reverts the patch whose unique name is passed in parameter.
	 * @param string $uniqueName
	 */
	public function revert($uniqueName): void {
		$patch = $this->get($uniqueName);
		$patch->revert();
	}
}
