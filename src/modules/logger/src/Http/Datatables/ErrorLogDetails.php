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



namespace Antares\Logger\Http\Datatables;

use Arcanedev\LogViewer\Exceptions\LogNotFound;
use Antares\Datatables\Services\DataTable;
use Antares\Logger\Utilities\LogViewer;
use Antares\Support\Collection;
use function trans;
use function app;

class ErrorLogDetails extends DataTable
{

    /**
     * internal datatable counter
     *
     * @var mixed
     */
    protected $counter = 0;

    /**
     * items per page
     *
     * @var mixed 
     */
    public $perPage = 25;

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $log     = $this->getLogOrFail(from_route('date'));
        $level   = from_route('level');
        $entries = $log->entries(is_null($level) ? 'all' : $level);
        $items   = [];
        $entries->each(function($item) use(&$items) {
            array_push($items, [
                'env'    => $item->env,
                'level'  => $item->level,
                'time'   => $item->datetime->format('Y-m-d H:i:s'),
                'header' => $item->header,
            ]);
        });
        return new Collection($items);
    }

    /**
     * Get a log or fail
     *
     * @param  string  $date
     *
     * @return Log|null
     */
    private function getLogOrFail($date)
    {
        try {
            return app(LogViewer::class)->get($date);
        } catch (LogNotFound $e) {
            return abort(404, $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $query = app('request')->ajax() ? $this->query() : $this->query()->forPage(1, $this->perPage);
        return $this->prepare($query)
                        ->editColumn('id', function () {
                            $this->counter++;
                            return $this->counter;
                        })->editColumn('env', function ($row = null) {
                            return '<span class="label label-env">' . $row['env'] . '</span>';
                        })->editColumn('level', function ($row) {
                            return '<span class="level level-' . $row['level'] . '">' . $row['level'] . '</span>';
                        })->editColumn('time', function ($row) {
                            return '<div style="width:150px;">' . format_x_days($row['time']) . '</div>';
                        })->editColumn('header', function ($row) {
                            $header = $row['header'];
                            return $this->smart_wordwrap(str_replace(['\\', '/', ":"], [' \\ ', ' / ', " "], $header), 150, '<br />');
                        })->addColumn('action', $this->getDetailsActionsColumn())
                        ->make(true);
    }

    /**
     * Smarter wordwrap
     * 
     * @param String $string
     * @param mixed $width
     * @param String $break
     * @return String
     */
    function smart_wordwrap($string, $width = 75, $break = "\n")
    {
        // split on problem words over the line length
        $pattern = sprintf('/([^ ]{%d,})/', $width);
        $output  = '';
        $words   = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        foreach ($words as $word) {
            if (false !== strpos($word, ' ')) {
                // normal behaviour, rebuild the string
                $output .= $word;
            } else {
                // work out how many characters would be on the current line
                $wrapped = explode($break, wordwrap($output, $width, $break));
                $count   = $width - (strlen(end($wrapped)) % $width);

                // fill the current line and add a break
                $output .= substr($word, 0, $count) . $break;

                // wrap any remaining characters from the problem word
                $output .= wordwrap(substr($word, $count), $width, $break, true);
            }
        }

        // wrap the final output
        return wordwrap($output, $width, $break);
    }

    /**
     * {@inheritdoc}
     */
    public function html($url = null)
    {
        $return = $this->setName('Error Log Details')
                ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('Id'), 'class' => 'desktop'])
                ->addColumn(['data' => 'env', 'name' => 'env', 'title' => trans('ENV'), 'class' => 'desktop'])
                ->addColumn(['data' => 'level', 'name' => 'level', 'title' => trans('Level'), 'class' => 'desktop'])
                ->addColumn(['data' => 'time', 'name' => 'time', 'title' => trans('Time'), 'class' => 'desktop'])
                ->addColumn(['data' => 'header', 'name' => 'header', 'title' => trans('Header'), 'class' => 'desktop'])
                ->setDeferedData();
        if (!is_null($url)) {
            $return->ajax($url);
        }
        return $return;
    }

    /**
     * Get actions column for table builder.
     * 
     * @return callable
     */
    protected function getDetailsActionsColumn()
    {
        return function ($row) {
            $btns    = [];
            $html    = app('html');
            $circles = [];
            foreach ([1, 2, 3] as $index) {
                array_push($circles, $html->create('i', '', ['class' => 'fa fa-circle']));
            }
            $section    = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            $indicators = $html->create('i', $html->raw(implode('', $circles)), ['class' => 'ma-trigger'])->get();
            return $html->raw(implode('', [$section, $indicators]))->get();
        };
    }

}
