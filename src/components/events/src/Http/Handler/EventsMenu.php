<?php

namespace Antares\Events\Http\Handler;

use Antares\Foundation\Support\MenuHandler;

class EventsMenu extends MenuHandler
{
    /**
     * Konfiguracja
     *
     * @var array
     */
    protected $menu = [
        'id' => 'events',
        'title' => 'Events',
        'link' => 'antares::events/index',
        'icon' => 'zmdi-explicit',
    ];

    /**
     * Określenie pozycji
     *
     * @return string
     */
    public function getPositionAttribute()
    {
        return '^:logger';
    }

    /**
     * Weryfikacja dostępu acl
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}