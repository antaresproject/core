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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Licensing\Adapter;

use Antares\Asset\Factory as AssetFactory;
use Antares\Asset\JavaScriptDecorator;
use Illuminate\Contracts\Config\Repository;
use Symfony\Component\Console\Application;
use function view;

class LicenseNotifier
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
     * executes license alert
     * 
     * @param type $data
     */
    public function run($data)
    {
        $container = $this->assetFactory->container('antares/foundation::scripts');
        $view      = view('antares/licensing::alert.html', ['data' => $data]);
        $container->inlineScript('license-error', $this->inline(JavaScriptDecorator::decorate($view->render())));
    }

    /**
     * generate sweetalert message box
     * 
     * @return String
     */
    protected function inline($html)
    {
        return <<<EOD
           $(document).ready(function(){                         
                attributes = {                    
                    text: $html,
                    dismissQueue: true,
                    layout: 'centerFull',
                    timeout: 10000
                };
                noty($.extend({}, APP.noti.errorFM("lg", "full"), attributes));
        });
EOD;
    }

}
