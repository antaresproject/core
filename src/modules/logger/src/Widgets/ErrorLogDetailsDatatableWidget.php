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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Logger\Widgets;

use Antares\Logger\Http\Datatables\ErrorLogDetails;
use Antares\UI\UIComponents\Templates\Datatables;
use Illuminate\Support\Facades\Route;

class ErrorLogDetailsDatatableWidget extends Datatables
{

    /**
     * name of widget
     * 
     * @var String 
     */
    public $name = 'Error Log Details Datatable Widget';

    /**
     * 
     *
     * @var type 
     */
    protected $attributes = [
        'titlable' => false,
    ];

    /**
     * Where widget should be available 
     *
     * @var array
     */
    protected $views = [
        'antares/logger::admin.index.details'
    ];

    /**
     * widget routes definition
     * 
     * @return \Symfony\Component\Routing\Router
     */
    public static function routes()
    {
        Route::post('error-log-details-list/{date}', ['middleware' => 'web', function() {
                return app(ErrorLogDetails::class)->ajax();
            }]);
    }

    /**
     * render widget content
     * 
     * @return String | mixed
     */
    public function render()
    {
        $table = app(ErrorLogDetails::class)->html('/error-log-details-list/' . from_route('date'));
        return view('antares/logger::admin.widgets.error_details_datatable_widget', ['dataTable' => $table]);
    }

}
