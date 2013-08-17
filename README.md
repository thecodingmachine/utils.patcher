Mouf's patching system
======================

This package is a patch system designed for [Mouf](http://mouf-php.com) than enables developers to know what patch has been run and what needs to be run on its environment.
If you are working with a team or with many environment, this is very useful to know which database patches have been applied or not in an environment.

Video tutorial
--------------

<iframe width="640" height="480" src="//www.youtube.com/embed/1uxO1qDSuZw" frameborder="0" allowfullscreen></iframe>

Installing the patch service
----------------------------

Installation is done via [composer](http://getcomposer.com). Here is a typical _composer.json_ file:


```json
{
	...
    "require": {
        "mouf/utils.patcher": "~1.0",
        "mouf/database.patcher": "~1.0"
    } 
}
```
As you can see, we are installing two packages here.

- **mouf/utils.patcher** contains the patch service. The patch service can be used to install any kinds of patches, but does not contain any patches implementation. This is why we need the second packages.
- **mouf/database.patcher** adds an easy way to create database patches (the most common use of the patch system).

Using the patch service
-----------------------

Once the patch service is installed, you will notice there is a new menu in Mouf UI.

<img src="doc/images/menu.png" />

Using the **Utils** > **Patches management** menu, you can access the patches list or create new database patches.

Let's have a quick look at the paches list.

<img src="doc/images/patch-list.png" />

In this list, you can view all the patches that have been defined. Using one big button, you can easily apply all the patches
that needs to be applied. This is really the only button you should ever touch on that screen, unless you are playing with advanced
features like database replication, etc...

If you need a more *fine-tuned* approach, you can **apply** each patch one by one. You can also 
**skip** the patch if you prefer to run it yourself or if you know it has already been applied.
Finally, you will notice that some patches can be **reverted**.


Creating/Editing a database patch
---------------------------------

You can create a new database patch using the **Utils** > **Patches management** > **Register a database patch** menu.

<img src="doc/images/edit-dbpatch.png" />

As you can see, you need to provide a unique patch name. You can (and you should) add a comment that will help
you and others remember what this patch is doing.
Finally, you will add the SQL of the patch.

You can choose what to do when you save the patch. You have 3 options:

- Most often, it is likely than when you save the patch, you already applied it in your development environment.
In this case, you should **skip** the patch (there is no point in applying this patch again).
- If you haven't applied the patch yet, you can choose to save and **apply** the patch.
- Finally, you can also choose to save, but **do not apply** the patch yet. In this case, the patch will be in **Awating** state. 
 
###Advanced options

There are a number of advanced options. These will allow you to:

- Choose the file saving the patch (the SQL of the patch is stored in its own file, usually in the **database/up** directory.
- Set up a *reverse patch* that can be used to cancel/revert your patch.


[You are a package developer? You want your own package to create/modify tables? See how you can use the patch system for that.](doc/for_packages_developer.md)  
[Want to learn more about the patch system? Want to learn how to create you own non db-related patches? Have a look at the advanced documentation.](doc/advanced.md)
