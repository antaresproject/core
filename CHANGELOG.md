# Changelog

### [0.9.2]

##### New

* Review project for missing frontend changes. 
* Env config for application bootstrap file (fix for root) 
* Env config for application bootstrap file. 
* Access to notifications helpers inside Module Service Provider. 
* Backend integration with new frontend. 
* Alert notification type to seed. 
* Backend integration with new frontend. 
* Response helper CHG: vue form JS assets. 
* Notification recipients field. 
* Vue form builder ADD: database actions context builder helper FIX: PHP docs CHG: database migration for notification CHG: form fieldset FIX: database wrong method construct ADD: for form builders which have v-model attribute the value should not be pass to value attribute. 
* Asset publisher links for symbolic assets. 
* Backend integration. 
* Dt filters. 
* Frontend integration. 
* Frontend integration. 
* Exception column to failed_jobs table. 
* Set position method to asset publisher. 
* Custom type for controls. 
* Twig functions to check who is authenticated. 
* Resolve display form errors for multiple tabular input. 
* Form errors for tabular input. 
* Deletes controls from fieldset. 
* Middleware for dashboard routing. 
* Support for modular css files. 
* Support for time and date formatters based on brand settings. 
* Support for date formatter based on brand. 
* Support for separated route files. 
* Subject column for notifications. It will be used as a subject for emails. 
* Form error twig extension for tabular input validation. 
* Support for reordering tables row, support for area contexts. 
* Datatables - support for reordering rows. 
* Menu tooltips descriptions. 
* Switch type for forms and dynamic errors for VUE forms. 
* New navigation system to handling breadcrumbs. 
* Remove unused presenter argument. 
* Url helper for create links without area. 
* Support for multiple filters in datatables. 
* Extend notification with messages. 
* Post helper method. 
* Html label in form controls. 
* Phpunit tests. 
* Phpunit per module. 
* Assets symlinker for local, router. 
* Assets symlinker for local, router. 
* Phpunits refactoring. 
* Readme.md, FIX: small fixes. 
* Add categories to acl. 
* Acl core module. 
* More usuable installation process. 
* Merge with 0.9.2.2. 
* .gitignore. 
* .gitignore. 
* Gitsubmodules. 
* Add modules dir. 
* Ui components, structure refactoring. 
* Initial commit. 
* Added unit tests for Control Types ADD: A few new Control Types CHG: Minor fixes and changes in Control Types CHG: Minor changes in form layout templates. 
* Log to extension operations FIX: tests for extensions and area (fix in config file) 
* Installation step with components selection. 
* Countries sql, multiuser. 
* Added decorators for controls CHG: Changes in control AbstracType. 
* LabelWrapper, inputWrapper CHG: Container for hidden type is hidden too. 
* When any label added to control, generates from name FIX: temporary fix for firing BeforeFormRender event. 
* Controls for Files and Dates. 
* New contrl types: Hidden, Password, Range CHG: Changed and new options added to Select. 
* SetSearch() feature for selects ADD: TimezoneType, LanguageType. 
* Info tooltip for labels. 
* Added support for Option groups for Select. 
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
* Activation operations for installing extensions.

##### Changes

* Updated notification migration for stacks CHG: add property attributes to form labels. 
* Update of version. 
* Update of area implementation. 
* Unit tests application path CHG: notification seeder and migration. 
* Commented phone number. 
* Backend integration with frontend. 
* Fast SMS adapter. 
* "booted" method for all service providers. 
* Possibility to set manually current area. 
* Removed Vue form builder. 
* Notification types seed (typo) 
* Notification types seed. 
* Removed PHP 7 strict type. 
* Added runtime cache for languages ADD: PHPDoc for extension repository. 
* Removed unnecessary query for user roles CHG: phpdoc for data table builder. 
* Some fixes for tests, CHG: dropzone control behaviour and template, CHG: removed deprecated notifier CHG: password reset service CHG: notifications CHG: some minor fixes or changes. 
* Optimization changes #2. 
* Optimization changes. 
* Slugging names for ACL with runtime cache CHG: disabled database crypt for increase performance FIX: PHPDoc for some classes. 
* Remove php 7.1 code occurences. 
* Area manager refactorization #2 ADD: area tests. 
* Area manager refactorization. 
* Small code reorganization. 
* Moved date and time formatter from helper to class. Performance improvements for date formatter. 
* Removed unused commented lines. 
* Removed subject from notification. 
* Change for resolving form controls values. 
* Changes quotes for form views. 
* New layout integration. 
* Update of notifications migration files. 
* Router fixes. 
* Notifier - notification channels. 
* Db cryptor disable config check. 
* Prerelease updated. 
* Minor and major changes. 
* Minor and major refactorings. 
* Minor refactoring. 
* :eyes: major changes. 
* Remove unsued .gitmodules file. 
* :book: update of README.md. 
* Database.sqlite3 - add two factor auth. 
* Changes for phpunit tests. 
* Phpunit tests - update of sqlite. 
* Jobs class inside foundation and AbstractNotificationTemplate.php use. 
* Code coverage and phpunit tests. 
* Changes of database.sqlite3. 
* Relocating ui components. 
* Move components to modules. 
* Installer views. 
* Antares Project -> Antares. 
* Composer json structure resolve. 
* Core composer.json. 

##### Fixes

* More fixes for RWD in release. 
* Looking for app file for unit tests during travis building (removed due wrong loop) 
* Looking for app file for unit tests during travis building. 
* Accessing to DI key in array. 
* Checking if current route is not null FIX: installation contract parameters CHG: extra method for module service provider which is similar to boot method but is executing after booted all extensions. 
* Breadcrumbs submenu path. 
* Datatable constructor CHG: removed commented code. 
* Fixes for backend integration. 
* Setting area from user. 
* Variable assignation CHG: refactored module service provider ADD: variable adapter of foundation for notification CHG: assetic refactoring and performance. 
* Restore widgets routes for client area. 
* Password reset notification (without editable notification message yet) 
* Required service provider for tests. 
* For raw notification message (fetched from database) content type must be html instead of plain text. 
* Retrive brand date options. 
* Fix for fetching form errors. 
* Quick search - update for url response param. 
* Module name resolver for root path. 
* Fix for helpers. 
* Module name resolver. 
* Acl will be properly setup for modules. 
* Breadcrumb class namespace. 
* After laravel update. 
* Removed invalid class for notification provider; updated method for user role. 
* Support for ajax form validation. 
* Twig tooltip extension. 
* :fire: minor and major fixes. 
* Remove throw exception. 
* Remove invalid service providers. 
* Fixed paths for themes. 
* Remove unused ExampleTest, remove registration controller test. 
* Migration file for ui components, fix location. 
* Fixes for installation. 
* Installer fixes. 
* Antares/control -> antares/acl.  

##### Internal

* Clear repo. 
* Merge. 
* Resolved conflicts. 
* Merged conflicts. 
* Resolved conflicts. 
* Fixed data typing. 
* Refactorization and comments fixes. 
* Merged 0.9.2 branch INT: return types for methods. 
* Updated core from branch for Laravel 5.4. 

##### Other

* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Merge remote-tracking branch 'origin/0.9.2' into 0.9.2. 
* Merge pull request #16 from antaresproject/0.9.2-laravel5.5. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 

  # Conflicts:
  #	src/utils/testing/src/CreateApplication.php

* Merge remote-tracking branch 'origin/0.9.2' into 0.9.2. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 

  # Conflicts:
  #	src/components/view/src/Console/Command.php
  #	src/foundation/src/Providers/FoundationServiceProvider.php
  #	src/ui/components/datatables/src/Html/Builder.php

* Merge remote-tracking branch 'origin/0.9.2' into 0.9.2. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Merge remote-tracking branch 'origin/0.9.2' into 0.9.2. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Update composer.json. 
* Merge remote-tracking branch 'origin/0.9.2' into 0.9.2. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Merge remote-tracking branch 'origin/0.9.2' into 0.9.2. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Update composer.json. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Update DashboardController.php. 
* Add files via upload. 
* Update composer.json. 
* Update ApplicationTestCase.php. 
* Update composer.json. 
* Add files via upload. 
* Update composer.json. 
* Merge with master. 
* Merge pull request #14 from antaresproject/0.9.2.2. 

  0.9.2.2

* Merge pull request #9 from antaresproject/0.9.2-dev2. 
* Merge branch '0.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Merge branch '-2.9.2' of https://github.com/antaresproject/core into 0.9.2. 
* Merge remote-tracking branch 'composer/0.9.2' into 0.9.2. 

  # Conflicts:
  #	extension/src/Processors/Acl.php

