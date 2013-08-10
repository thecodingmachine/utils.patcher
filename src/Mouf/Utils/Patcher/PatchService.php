<?php
/*
 Copyright (C) 2013 David Négrier - THE CODING MACHINE

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
/**
 * The patch service is in charge of applying a list of patches attached to this application.
 * Especially, it contains the list of patch that has ever been declared.
 * 
 * @author David Negrier <david@mouf-php.com>
 */
class PatchService implements MoufValidatorInterface {

	/**
	 * The list of patches declared for this application.
	 * 
	 * @var PatchInterface[]
	 */
	private $patchs = array();

	/**
	 * The list of patches declared for this application.
	 * @param array $patchs
	 * @return PatchService
	 */
	public function setPatchs(array $patchs) {
		$this->patchs = $patchs;
		return $this;
	}
	
	const IFEXISTS_EXCEPTION = "exception";
	const IFEXISTS_IGNORE = "ignore";
	
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
			if ($ifExists == self::IFEXISTS_IGNORE) {
				return $this;
			} else {
				throw new PatchException("The patch '$patch->getUniqueName()' is already registered.");
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
			if ($patch->getUniqueName() == $uniqueName) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns the number of patches that needs to be applied.
	 * 
	 * @return int
	 */
	public function getNbAwaitingPatchs() {
		$cnt = 0;
		foreach ($this->patchs as $patch) {
			if ($patch->getStatus() == PatchInterface::STATUS_AWAITING) {
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
		$instanceName = MoufManager::getMoufManager()->findInstanceName($this);
		
		if ($nbAwaitingPatchs == 0) {
			if ($nbPatchs == 0) {
				return new MoufValidatorResult(MoufValidatorResult::SUCCESS, "<strong>Patcher</strong> No patches declared");
			} elseif ($nbPatchs == 0) {
				return new MoufValidatorResult(MoufValidatorResult::SUCCESS, "<strong>Patcher</strong> The patch has been successfully applied");
			} else {
				return new MoufValidatorResult(MoufValidatorResult::SUCCESS, "<strong>Patcher</strong> All $nbPatchs patches have been successfully applied");
			}
		} else {
			return new MoufValidatorResult(MoufValidatorResult::WARN, "<strong>Patcher</strong> There are <strong>$nbAwaitingPatchs</strong> patches awaiting to be applied. <a href='".ROOT_URL."mouf/mouf/patcher/?instanceName=$instanceName' class='btn btn-large btn-primary'>Apply the patches</a>.");
		}	
	}
}

