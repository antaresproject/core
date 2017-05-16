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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Builder;

use Antares\Asset\Factory as AssetFactory;
use Illuminate\Contracts\Config\Repository;
use Antares\Updater\Contracts\RedAlert as RedAlertContract;
use Antares\Updater\Contracts\Adapter as AdapterContract;
use Antares\Asset\JavaScriptDecorator;
use Illuminate\View\View;

class RedAlert implements RedAlertContract
{

    /**
     * @var AssetFactory 
     */
    protected $assetFactory;

    /**
     * @var Repository 
     */
    protected $config;

    /**
     * default alert attributes
     *
     * @var array 
     */
    protected $attributes = [
        'title'              => "New system version available",
        'text'               => '',
        'html'               => true,
        'confirmButtonColor' => "#DD6B55",
        'confirmButtonText'  => "Yes, update!",
        'showCancelButton'   => true,
        'cancelButtonText'   => "Don't show this dialog anymore",
        'closeOnConfirm'     => false,
        'closeOnCancel'      => false,
        'width'              => "800"
    ];

    /**
     * default messages container
     *
     * @var array
     */
    protected $messages = [
        'title'             => 'New system version available',
        'confirmed_title'   => 'OK',
        'confirmed_message' => 'This window will no longer show up in the future.',
        'update_title'      => 'OK',
        'update_message'    => 'redirect...'
    ];

    /**
     * constructing
     * 
     * @param Application $app
     */
    public function __construct(Repository $config, AssetFactory $assetFactory)
    {
        $this->config       = $config;
        $this->assetFactory = $assetFactory;
    }

    /**
     * frontend scripts appender
     * 
     * @return AssetFactory
     */
    public function scripts()
    {
        return publish('updater', 'scripts.resources');
    }

    /**
     * default alert builder 
     * 
     * @param View $view
     * @param array $attributes
     * @param array $messages
     */
    public function build(View $view, array $attributes = array(), array $messages = array())
    {
        $container = $this->scripts();

        $this->setAttributes($attributes);
        $this->setMessages($messages);
        $html = str_replace([PHP_EOL, "\n"], '', $view->render());
        $container->inlineScript('version-box', $this->inline($html));
    }

    /**
     * build with adapter
     * 
     * @param AdapterContract $adapter
     */
    public function buildWithAdapter(AdapterContract $adapter)
    {
        $container = $this->scripts();

        $version     = $adapter->getVersion();
        $changelog   = $adapter->getChangeLog();
        $description = $adapter->getDescription();
        $html        = str_replace([PHP_EOL, "\n"], '', view('antares/updater::admin.partials._alert', compact('version', 'description', 'changelog'))->render());
        /**
         * @todo przeniesc do konfiga
         */
        $hideUrl     = handles('antares::updater/hide');
        $redirectUrl = handles('antares::updater/update');

        $container->inlineScript('version-box', $this->inline($html, $hideUrl, $redirectUrl));
    }

    /**
     * generate sweetalert message box
     * 
     * @return String
     */
    protected function inline($html, $hideUrl = null, $redirectUrl = null)
    {
        $params = JavaScriptDecorator::decorate($this->attributes($html));

        $afterRender = $this->afterRender($redirectUrl, $hideUrl);
        $swal        = "swal($.extend({}, APP.swal.cb1Warning(), $params));";
        if (strlen($afterRender) > 0) {
            $swal = "swal($.extend({}, APP.swal.cb1Warning(), $params),$afterRender);";
        }
        $inline = <<<EOD
           $(document).ready(function(){                         
            $swal
        });
EOD;

        return sprintf($inline, $redirectUrl, $this->messages['update_title'], $this->messages['update_message'], $hideUrl, $this->messages['confirmed_title'], $this->messages['confirmed_message']);
    }

    /**
     * after render js code
     * 
     * @param String $redirectUrl
     * @param String $hideUrl
     * @return string
     */
    protected function afterRender($redirectUrl = null, $hideUrl = null)
    {
        $string = '';
        if (is_null($redirectUrl) && is_null($hideUrl)) {
            return $string;
        }
        if (!is_null($redirectUrl) && is_null($hideUrl)) {
            $inline = <<<EOD
           function(isConfirm) {   
                  if (isConfirm) {     
                      %s
                  }
                }
EOD;
            $string = sprintf($inline, $this->redirectUrl($redirectUrl));
        }
        if (!is_null($hideUrl) && is_null($redirectUrl)) {
            $inline = <<<EOD
           function(isConfirm) {   
                  if (isConfirm===undefined || isConfirm===false) {     
                      %s
                  }
                }
EOD;
            $string = sprintf($inline, $this->hideUrl($hideUrl));
        }
        if (!is_null($hideUrl) && !is_null($redirectUrl)) {
            $inline = <<<EOD
           function(isConfirm) {   
                  if (isConfirm) {     
                    %s
                  }else{
                    %s
                  }
           }
EOD;
            $string = sprintf($inline, $this->redirectUrl($redirectUrl), $this->hideUrl($hideUrl));
        }
        return $string;
    }

    /**
     * create inline redirect js code
     * 
     * @param String $redirectUrl
     * @return string
     */
    private function redirectUrl($redirectUrl = null)
    {
        if (is_null($redirectUrl)) {
            return '';
        }
        $inline = <<<EOD
                      window.location.href="%s";                
                      swal("%s","%s",'success');   
EOD;
        return sprintf($inline, $redirectUrl, $this->messages['update_title'], $this->messages['update_message']);
    }

    /**
     * hide url ajax
     * 
     * @param String $hideUrl
     * @return string
     */
    private function hideUrl($hideUrl = null)
    {
        if (is_null($hideUrl)) {
            return '';
        }
        $inline = <<<EOD
                      $.ajax({
                            url:"%s",
                            success:function(){
                                swal('%s','%s','error');
                            },
                            error:function(response){
                                swal('Error',response.message,'error');
                            }
                      });                      
EOD;
        return sprintf($inline, $hideUrl, $this->messages['confirmed_title'], $this->messages['confirmed_message']);
    }

    /**
     * gets alert attributes
     * 
     * @param String $html
     * @return array
     */
    protected function attributes($html)
    {
        return array_merge($this->attributes, ['html' => $html]);
    }

    /**
     * attributes setter
     * 
     * @param array $attributes
     * @return \Antares\Updater\Builder\RedAlert
     */
    public function setAttributes(array $attributes = array())
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * messages setter
     * 
     * @param array $messages
     * @return \Antares\Updater\Builder\RedAlert
     */
    public function setMessages(array $messages = array())
    {
        $this->messages = array_merge($this->messages, $messages);
        return $this;
    }

}
