Mouf's patching system
======================

This package is a patch system designed for [Mouf](http://mouf-php.com) than enables developers to know what patch has been run and what needs to be run on its environment.
If you are working with a team or with many environment, this is very useful to know which database patches have been applied or not in an environment.

Inside the patch service
------------------------

At the core of Mouf's patch system, there is the **patch service**.
The patch service is in charge of referencing all patches that can be applied to your application.
As such, it contains a list of patches to be applied.

The patch service is represented by the <code>PatchService</code> class. If comes with a default instance that is installed in Mouf with the package.
The default instance name if *patchService*.

The <code>PatchService</code> class registers <code>PatchInterface</code> instances (using the **registerPatch** method).

```php
Mouf::getPatchService()->register($myPatch);
```
 
The patch service is 'implementation agnostic'. It does not know if you are installing a patch related to the database, or to the file system, or whatever.
It does not even know if a patch has been run or not. This is delegated to the patch objects (implementing the <code>PatchInterface</code>).

Very often, the patch service is used to track database patches. But you can use the patch service to patch anything you want. You just have to implement
the <code>PatchInterface</code> to be able to create your own patch.

```php
interface PatchInterface {
	...
	/**
	 * Applies the patch.
	 */
	function apply();

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
}
```

As you can see from the (simplified) interface, a patch can be **applied**, optionally **reverted**.
Each patch must have a unique name, and can provide a description. It is the responsability of the patch object to track its own status (it is **not**
the responsibility of the PatchService to do this).