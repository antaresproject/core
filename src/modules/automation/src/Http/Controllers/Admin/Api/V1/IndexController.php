<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Automation
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\Http\Controllers\Admin\Api\V1;

use Antares\Automation\Http\Controllers\Admin\IndexController as BaseController;

class IndexController extends BaseController
{

    /**
     * {@inheritdoc}
     */
    public function setupMiddleware()
    {
        parent::setupMiddleware();
    }

    /**
     * Automation list
     * @api {get} /api/v1/antares/automation/index Automation list
     * @apiDescription Returns details about all automations
     * @apiName Automation list
     * @apiGroup Automation
     * @apiVersion 0.9.2
     * @apiParam (Request Parameters) {String} [page] Page
     * @apiParam (Request Parameters) {String} [per_page] Rows limit per page
     * @apiHeader (Request Headers) {String} Authorization Authorization type.
     * @apiHeader (Request Headers) {String} Accept Accepted content type.
     * @apiExample {curl} CURL Example
     *      curl -X GET -H "Authorization: Bearer xyz"
     *          -H "Accept: application/vnd.antares.v1+json"
     *          "http://localhost/api/v1/antares/automation/index?per_page=1&page=1"
     * @apiSuccessExample {json} Success-Response
     * {"total":2,"per_page":10,"current_page":1,"last_page":1,"next_page_url":null,"prev_page_url":null,"from":1,"to":2,"data":[{"id":4,"component_id":8,"category_id":4,"active":1,"name":"module:run","value":{"component_id":8,"category_id":4,"standalone":false,"description":"Sample module automation job","title":"Sample Automation Job","cron":"*\/2 * * * *","launch":"daily","launchTimes":["everyFiveMinutes","everyTenMinutes","everyThirtyMinutes","hourly","daily"],"classname":"Antares\\Module\\Console\\ModuleCommand"},"created_at":"2017-01-06 16:45:42","updated_at":"2017-01-06 16:45:42","job_results":[],"component":{"id":8,"name":"module","full_name":"Module","description":"Custom Module","status":true,"path":"base::src\/components\/module","author":"\u0141ukasz Cirut","url":"https:\/\/antaresproject.io","version":"0.9.0","order":1,"options":[]},"category":{"id":4,"name":"custom","title":"Custom"}},{"id":5,"component_id":9,"category_id":4,"active":1,"name":"notifications:remove-olds","value":{"component_id":9,"category_id":4,"standalone":false,"description":"Remove notification logs after days configured in general settings section.","title":"Notifications remover","cron":"*\/2 * * * *","launch":"daily","launchTimes":["everyFiveMinutes","everyTenMinutes","everyThirtyMinutes","hourly","daily"],"classname":"Antares\\Notifications\\Console\\NotificationsRemover"},"created_at":"2017-01-06 16:45:42","updated_at":"2017-01-06 16:45:42","job_results":[],"component":{"id":9,"name":"notifications","full_name":"Notifications","description":"Notifications Manager Antares","status":true,"path":"base::src\/components\/notifications","author":"\u0141ukasz Cirut","url":"https:\/\/antaresproject.io","version":"0.9.0","order":1,"options":[]},"category":{"id":4,"name":"custom","title":"Custom"}}]}
     * @apiErrorExample {json} Error Response
     * {"message": "Error Message","status_code": 500}
     * @apiSuccess (Response Parameters) total Number of results
     * @apiSuccess (Response Parameters) per_page Number of results per page
     * @apiSuccess (Response Parameters) current_page Current page of results
     * @apiSuccess (Response Parameters) last_page Number of the last page with results
     * @apiSuccess (Response Parameters) next_page_url Url to the following  page
     * @apiSuccess (Response Parameters) prev_page_url Url to the previous page from Beginning number of results on the current page to Ending number of results on the current page
     * @apiSuccess (Response Parameters) data Results
     * @apiSuccess (Response Parameters) id Automation identifier
     * @apiSuccess (Response Parameters) component_id Component identifier
     * @apiSuccess (Response Parameters) category_id Category identifier
     * @apiSuccess (Response Parameters) active Automation activity
     * @apiSuccess (Response Parameters) name Command name
     * @apiSuccess (Response Parameters) category Category details
     * @apiSuccess (Response Parameters) component Component details
     * @apiSuccess (Response Parameters) value Automation details
     * @apiSuccess (Response Parameters) job_results List of job results
     * @apiSuccess (Response Parameters) created_at Automation creation date
     * @apiSuccess (Response Parameters) updated_at Automation last updated date
     */
    public function index()
    {
        return parent::index();
    }

}
