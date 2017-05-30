# Changelog

### [0.9.2]

##### New

* Installation step with components selection.
* Countries sql, multiuser.
* Added decorators for controls CHG: Changes in control AbstracType.
* LabelWrapper, inputWrapper CHG: Container for hidden type is hidden too.
* When any label added to control, generates from name FIX: temporary fix for firing BeforeFormRender event.
* Controls for Files and Dates.
* New contrl types: Hidden, Password, Range CHG: Changed and new options added to Select.
* SetSearch() feature for selects ADD: TimezoneType, LanguageType.
* Info tooltip for labels.
* Base contracts and traits ADD: prependHtml, appendHtml to control ADD: Country select with flags.
* New form control types.
* Input wrapper.
* New form control type: SelectType.
* Validation messages.
* Default label view.
* Labels are separated objects.
* Added view template files for Form control types CHG: Changes in Fieldset/Form renderers to support new Form control types.
* AbstractDecorator for Form controls, Text and Textarea control CHG: changes in Form control AbstractType.
* Abstract Types for Form controls.
* Text translation.
* Option to skip composer for installation command from artisan.
* Support custom URL for extension settings form.
* Artisan command to flush core ACL FIX: extension ACL command signature.
* Listener for failed composer when installing extension.
* Activation operations for installing extensions FIX: console outputs FIX: path resolver for extension finder.
* Unit tests refactoring.
* Testing  - add custom service providers, remove unused class.
* Assets publisher from component namespace, FIX: migrating component files.
* Laravel 5.4 integration.
* Laravel 5.3 integration.
* Laravel 5.3 integration.
* Horizontal form view as default.
* Routes->handles replacement, route naming with area, datatables order adapter.
* Important:Filters/sorting in datatables should be saved (browser cache) and be remembered after refreshing the page.
* Relese 0.9.2.
* Refactoring phpunit.
* Notification logs.
* Notification templates.
* Dependable activity actions.
* Phpunits refactoring.
* Phpunits refactoring.
* Phpunit refactoring.
* Travis configuration file, phpunit.
* Travis configuration file, phpunit.
* Datatables filtering columns.
* Datatable natvive column filter.
* Default settings on fresh install.
* Session datatables per page saver.

##### Changes

* Readme.md structure change.
* Improved rendering hidden type FIX: Fixed wrappers.
* Changes in Antares Form Builder to handle new form control types.
* Authors ordering for extensions datatable.
* Output errors for composers ADD: filtering extensions by type.
* Extensions types moved to model ADD: filter search by extension type.
* Show configuration for only for active components.
* Added extensions.js to the public directory.
* Acl migration facades and added tests.
* Acl migration tests FIX: added ginignore to view cache directory.
* Refactored ACL importer FIX: foundation and kernel files to handle the laravel 5.4 version.
* Composer output FIX: dispatcher method for Laravel 5.4 ADD: extension tests CHG: installer config facade FIX: installation worker.
* Quick search engine based on datatables results.
* SIMPLIFY installation process, FIX: multiple same buttons in mass actions.
* Phpunits for modules.
* Refactoring unit tests after laravel 5.4 integration.
* Brands breadcrumbs changes, asset webpack ignore minify, customfield protection, datatables - force disable scripting, FIX: exception 500 & 404 logos.
* Core phpunit configuration changes.
* Datatables pre init html builder, ADD: phpunit configuration file.
* Datatables pre init html builder, ADD: phpunit configuration file.

##### Fixes

* Installation process - add defered activation events.
* Installation fixes.
* Scrolling console preview during installation FIX: stop progress installation and AJAX requests after close preview window.
* Unable to reload acl permissions.
* Unable to reload acl permissions.
* Add logs to installation process,installing custom modules.
* Invalid command on windows oses.
* Fixes for widgets finder.
* Components table view.
* Installer and extension unit test fixes.
* Templates after adding decorators.
* Country and Language fix.
* Fileupload fix.
* Unit tests.
* Tests.
* Extension autoloader.
* Resolving component name based on short one.
* Installation progress for system and extensions FIX: components datatables ADD: type, friendly name, homepage for components FIX: do not allow to uninstall core components.
* Removed the backup:db artisan console due to invalid configuration ADD: composer vendor directory validation for installation FIX: removed generating key for .env file after composer update command.
* Artisan console which uninstall a whole application FIX: installation stuck during progress.
* Changed old function getToken() to token(0 for session object in Kernel INT: removed step from installer in which user can select components.
* Wrong directory name for extension contracts INT: added more tests for extension module ADD: acl command first of all flush permissions and then import default permissions FIX: wrong name for activator/deactivator for publisher.
* Steps numbers, url to create admin.
* Saving acl of extensions with old names CHG: added command to refresh extensions ACL.
* Progress and installation logic CHG: removed license step from installation CHG: additional methods for extension manager FIX: extensions events FIX: components branches.
* Wrong route names in extension settings form FIX: extension name for asset manager.
* Composer installation.
* Installer service provider - invalid booting.
* Geoip invalid location resolver, invalid providers parameter in test benchamrk.
* Cssinline convertion in notifier.
* Staff -> Users has wrong main menu visible.
* Builder headers.
* Phpunits core pre release refactoring.
* Datatables table builder, ADD: phpunit configuration file.
* User add with customfields.

##### Internal

* Fixed data typing.
* Refactorization and comments fixes.

##### Other

* Fixes for composers and installer.
* Fixed wrong config facade.
* Removed composer custom installer.
* Adapted to new extensions installation. Init.
* Update composer.json.
* Update composer.json.
* Update composer.json.
* Update composer.json.
* Changed project name on composer.
* INITIAL ANTARES COMMIT.
* Initial commit.


