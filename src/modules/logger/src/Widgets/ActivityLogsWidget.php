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

use Antares\Logger\Http\Filter\ActivityTypeWidgetFilter;
use Antares\UI\UIComponents\Adapter\AbstractTemplate;
use Antares\Datatables\Adapter\FilterAdapter;
use Illuminate\Support\Facades\Request;
use Antares\Logger\Model\LogPriorities;
use Illuminate\Support\Facades\Route;
use Antares\Logger\Model\LogTypes;

class ActivityLogsWidget extends AbstractTemplate
{

    /**
     * How many items per page
     */
    const perPage = 10;

    /**
     * name of widget
     * 
     * @var String 
     */
    public $name = 'Activity Logs';

    /**
     * Title of widget in top bar
     *
     * @var String 
     */
    protected $title = 'Recent Activity';

    /**
     * widget attributes
     *
     * @var array
     */
    protected $attributes = [
        'min_width'      => 6,
        'min_height'     => 6,
        'max_width'      => 12,
        'max_height'     => 52,
        'default_width'  => 7,
        'default_height' => 16,
        'enlargeable'    => false,
        'titlable'       => true,
        'card_class'     => 'card--logs card--scrollbox',
    ];

    /**
     * Base url for pagination
     *
     * @var String 
     */
    protected static $baseUrl = '/widget/activity';

    /**
     * Definition of widget routes
     * 
     * @return \Symfony\Component\Routing\Router
     */
    public static function routes()
    {
        return Route::match(['GET', 'POST'], self::$baseUrl . '/{users?}', ['middleware' => 'web', function() {
                        return view('antares/logger::admin.widgets.logs', self::getParams());
                    }]);
    }

    /**
     * Widget params getter
     * 
     * @return array
     */
    protected static function getParams()
    {
        $adapter  = app(FilterAdapter::class)->add(ActivityTypeWidgetFilter::class);
        $request  = app('request');
        $priority = $request->get('priority');
        $search   = $request->get('search');
        $logs     = self::filter($priority, $search);
        return [
            'select_url'        => self::getBaseUrl(['page' => 1, 'per_page' => self::perPage, 'search' => '']),
            'search_url'        => self::getBaseUrl(['page' => 1, 'per_page' => self::perPage, 'priority' => null]),
            'url'               => self::getBaseUrl(),
            'selected_priority' => $priority,
            'search'            => $search,
            'types'             => app(LogTypes::class)->all(),
            'logs'              => $logs,
            'pagination'        => $logs->links('antares/foundation::layouts.antares.partials.pagination._pagination'),
            'priorites'         => app(LogPriorities::class)->all(),
            'filters'           => $adapter->getFilters('antares/logger::admin.widgets.log_filter')
        ];
    }

    /**
     * Base url getter
     * 
     * @param array $only
     * @return String
     */
    public static function getBaseUrl(array $only = ['page' => 1, 'per_page' => 20, 'priority' => null, 'search' => ''])
    {
        $request = app('request');
        $return  = [];
        foreach ($only as $name => $default) {
            array_set($return, $name, $request->get($name, $default));
        }
        $url    = self::$baseUrl;
        if (!is_null($userId = from_route('user'))) {
            $url = $url . '/' . $userId;
        }

        return $url . '?' . http_build_query($return, '', '&');
    }

    /**
     * render widget content
     * 
     * @return String | mixed
     */
    public function render()
    {
        publish('logger', ['js/activity_logs_widget.js']);
        return view('antares/logger::admin.widgets.logs', self::getParams())->render();
    }

    /**
     * Filters eloquent results
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected static function filter($priority = null, $search = null)
    {
        $key    = uri() . '.' . ActivityTypeWidgetFilter::class;
        $values = app('request')->session()->get($key);
        $userId = from_route('user');

        $ignore = config('logger.ignore_logs_in_widget');
        $query  = \Antares\Logger\Model\Logs::withoutGlobalScopes()
                ->select(['tbl_logs.*'])
                ->leftJoin('tbl_log_types', 'tbl_logs.type_id', 'tbl_log_types.id')
                ->leftJoin('tbl_logs_translations', 'tbl_logs.id', 'tbl_logs_translations.log_id')
                ->where('tbl_logs_translations.lang_id', lang_id())
                ->where('tbl_logs.brand_id', brand_id())
                ->where('tbl_log_types.active', 1);
        if (!empty($ignore)) {
            //$query->whereNotIn('tbl_log_types.name', $ignore);
        }


        $admin = user()->hasRoles('super-administrator') or user()->hasRoles('administrator');
        if (!$admin) {
            $uid       = auth()->user()->id;
            $query->whereRaw('(tbl_logs.user_id=' . $uid . ' or tbl_logs.author_id=' . $uid . ')');
            $priorites = LogPriorities::where('num', '<=', '3')->get()->pluck('id')->toArray();
            $query->where('tbl_logs.created_at', '>=', date('Y-m-d H:i:s', time() - 172800))->whereIn('priority_id', $priorites);
        } elseif (is_null($userId) && $admin) {
            $roles  = auth()->user()->roles;
            $childs = [];
            foreach ($roles as $role) {
                $childs = array_merge($childs, $role->getChilds(), [$role->id]);
            }
            $query->leftJoin('tbl_user_role', 'tbl_logs.user_id', 'tbl_user_role.user_id');
            $query->where(function($query) use($childs) {
                $query->whereIn('tbl_user_role.role_id', array_values($childs))->orWhere('tbl_logs.user_id', null);
            });
            $priorites = LogPriorities::where('num', '<=', '3')->get()->pluck('id')->toArray();
            $query->where('tbl_logs.created_at', '>=', date('Y-m-d H:i:s', time() - 172800))
                    ->whereIn('priority_id', $priorites);
        } else {
            $query->where('tbl_logs.user_id', $userId);
        }

        if (!empty($values)) {
            $query->whereIn('tbl_logs.type_id', array_values($values['value']));
        }

        if (!is_null($search) && strlen($search) > 0) {
            $searchUp   = mb_strtoupper($search);
            $searchDown = mb_strtolower($search);
            $query->whereRaw("(tbl_logs_translations.raw like  \"%{$searchUp}%\" OR tbl_logs_translations.raw like  \"%{$searchDown}%\")");
        }
        $logs = $query->orderBy('tbl_logs.created_at', 'desc')->paginate(Request::get('per_page', self::perPage));

        if (!is_null($userId)) {
            $logs->setPath(self::$baseUrl . '/' . $userId . '/');
        } else {
            $logs->setPath(self::$baseUrl);
        }

        return $logs;
    }

}
