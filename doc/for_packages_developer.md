Using the patch system from a package
=====================================

If you are a package developer, there are chances that when installing your package, there are needs to modify the database structure (usually to create tables).
The database patch system comes with a utility class that will help you to register database patches when your package is installed.

First, if you are not used to install processes, [read the manual about install processes](http://mouf-php.com/packages/mouf/mouf/doc/install_process.md).

Now, all you need to know is there is a simple one line instruction to register a database patch from your install file:

```php
DatabasePatchInstaller::registerPatch($moufManager, 
	"patchUniqueName",
	"The patch description",
	"vendor/mygroup/mypackage/database/up/myfile.sql", // SQL patch file, relative to ROOT_PATH
	"vendor/mygroup/mypackage/database/down/myfile.sql"); // Optional SQL revert patch file, relative to ROOT_PATH
```
