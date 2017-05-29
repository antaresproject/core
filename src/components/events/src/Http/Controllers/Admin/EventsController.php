<?php

namespace Antares\Events\Http\Controllers\Admin;

use Antares\Events\Events\AbstractEvent;
use Antares\Events\Http\DataTables\EventsDataTable;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Events\Services\EventsParserService;

class EventsController extends AdminController
{
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
//        $this->middleware("antares.can:antares/events::index-action", ['only' => ['index']]);
    }

    /**
     * Zwykle prezentacja listy
     */
    public function index()
    {
        (new EventsParserService(base_path(), AbstractEvent::class))->parse();
        return app(EventsDataTable::class)->render('antares/events::admin.events.index');
    }
}