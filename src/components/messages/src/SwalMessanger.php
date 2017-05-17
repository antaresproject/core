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


namespace Antares\Messages;

use Antares\Asset\Factory as AssetFactory;

class SwalMessanger
{

    /**
     * Default script position
     */
    const position = 'antares/foundation::scripts';

    /**
     * Default message type
     *
     * @var String
     */
    protected $type = 'success';

    /**
     * AssetFactory instance
     *
     * @var AssetFactory 
     */
    protected $assetFactory;

    /**
     * Constructing
     * 
     * @param AssetFactory $assetFactory
     */
    public function __construct(AssetFactory $assetFactory)
    {
        $this->assetFactory = $assetFactory;
    }

    /**
     * Type setter
     * 
     * @param String $type
     * @return \Antares\Messages\SwalMessanger
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Appends inline script to scripts container
     * 
     * @param String $title
     * @param String $text
     * @return String
     */
    public function message($title, $text = null)
    {
        if (!in_array($this->type, ['success', 'error', 'info', 'warning'])) {
            return '';
        }
        $container = $this->assetFactory->container(self::position);
        $type      = $this->resolveType();
        return $container->inlineScript('swal-message', $this->inline($type, $title, $text));
    }

    /**
     * Resolve type script method
     * 
     * @return String
     */
    protected function resolveType()
    {
        return 'cb1' . ucfirst($this->type);
    }

    /**
     * generate flash messanger inline scripts
     * 
     * @return String
     */
    protected function inline($type, $title, $text = null)
    {
        $html   = (string) $text;
        $inline = <<<EOD
           $(document).ready(function(){                 
                swal($.extend({}, APP.swal.$type(), {
                    title: '$title',
                    text: '$html',                    
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Ok',
                    closeOnConfirm: false,
                    closeOnCancel: true
                }));
        });
EOD;
        return $inline;
    }

}
