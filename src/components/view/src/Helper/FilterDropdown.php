<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\View\Helper;

use Illuminate\Support\Collection;
use function view;

class FilterDropdown extends AbstractHelper
{

    /**
     * attributes container
     *
     * @var array 
     */
    protected $attributes = [
        'default' => false
    ];

    /**
     * helper view path
     *
     * @var String 
     */
    protected $view = 'view-helpers::filter-dropdown';

    /**
     * renders helper content
     * 
     * @return String
     */
    public function render()
    {

        $this->scripts();
        $dataProvider     = $this->prepareData();
        $this->attributes = array_merge($this->attributes, [
            'dataProvider' => $dataProvider,
            'emptyUrl'     => $this->emptyUrl()
        ]);
        if (!is_null($this->attributes['selected'])) {
            $this->attributes['selected'] = $dataProvider->where('id', (int) $this->attributes['selected'])->first()['name'];
        } else {
            $this->attributes['selected'] = $this->attributes['title'];
        }

        $view = view('view-helpers::filter-dropdown', $this->attributes);
        return $view->render();
    }

    /**
     * prepares data provider items
     * 
     * @return array
     */
    protected function prepareData()
    {
        $dataProvider = $this->attributes['dataProvider'];
        $urlPattern   = $this->attributes['urlPattern'];
        $return       = new Collection();
        foreach ($dataProvider as $item) {
            $name = isset($item->title) ? $item->title : $item->name;
            $url  = str_replace('{id}', $item->id, $urlPattern);
            $return->push(['url' => handles($url), 'name' => ucfirst($name), 'id' => $item->id]);
        }
        return $return->sortBy('name');
    }

    /**
     * generates empty url when no item seletected
     * 
     * @return Srting
     */
    protected function emptyUrl()
    {
        $urlPattern = $this->attributes['urlPattern'];
        $pattern    = '/[\[{\(].*[\]}\)]/U';
        return trim(preg_replace($pattern, '', $urlPattern), '/');
    }

    /**
     * inline scripts used by helper
     */
    protected function scripts()
    {
        $container = $this->app->make('antares.asset')->container('antares/foundation::scripts');
        $container->inlineScript('version-box', $this->inline());
    }

    /**
     * generate sweetalert message box
     * 
     * @return String
     */
    protected function inline()
    {
        $id       = $this->attributes['id'];
        $targetId = $this->attributes['targetId'];
        $inline   = <<<EOD
           $(document).ready(function(){                                         
                $('section.main-content').on('click','#$id a',function(e){
                    e.preventDefault();
                    handler=$(this);
                    $.ajax({
                        url:handler.attr('href'),
                        success:function(response){
                            $('#$targetId .btn--dropdown').html(handler.text());
                            $('#$targetId').parent().html(response);                            
                        }
                    });
                    return false;
                });
        });
EOD;
        return $inline;
    }

}
