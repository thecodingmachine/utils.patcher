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

/**
 * Classes implementing this interface represent patches that can be applied on the application.
 * 
 * @author David Negrier <david@mouf-php.com>
 */
interface PatchInterface {

	const STATUS_AWAITING = "awaiting";
	const STATUS_APPLIED = "applied";
	const STATUS_SKIPPED = "skipped";
	const STATUS_ERROR = "error";
	
	/**
	 * Applies the patch.
	 */
	function apply();
	
	/**
	 * Skips the patch (sets its status to "skipped").
	 */
	function skip();

	/**
	 * Reverts (cancels) the patch.
	 * Note: patchs do not have to provide a "revert" feature (see canRevert method).
	 */
	function revert();
	
	/**
	 * Returns true if this patch can be canceled, false otherwise.
	 * 
	 * @return boolean
	 */
	function canRevert();
	
	/**
	 * Returns the status of this patch.
	 * 
	 * Can be one of:
	 * 
	 * - PatchInterface::STATUS_AWAITING (patch awaiting to be applied)
	 * - PatchInterface::STATUS_APPLIED (patch has been run successfully)
	 * - PatchInterface::STATUS_SKIPPED (patch has been skipped)
	 * 
	 * @return string
	 */
	function getStatus();
	
	/**
	 * Returns a unique name for this patch. 
	 *
	 * @return string
	 */
	function getUniqueName();
	
	/**
	 * Returns a short description of the patch.
	 * 
	 * @return string
	 */
	function getDescription();
	
	/**
	 * Returns the error message of the last action performed, or null if last action was successful.
	 * 
	 * @return string
	 */
	function getLastErrorMessage();
	
	/**
	 * Returns the URL that can be used to edit this patch.
	 * This can return null if such an URL does not exist.
	 * 
	 * @return string
	 */
	function getEditUrl();
}

