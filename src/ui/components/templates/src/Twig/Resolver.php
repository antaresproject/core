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
 * @package    Ui\UiComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Twig;

use Antares\Ui\UIComponents\Contracts\GridStack;
use Illuminate\Support\Facades\Log;
use Twig_SimpleFunction;
use Twig_Extension;
use Exception;

class Resolver extends Twig_Extension
{

    /**
     * gridstack adapter instance
     *
     * @var GridStack 
     */
    protected $gridStackAdapter;

    /**
     * constructing
     * 
     * @param GridStack $gridStackAdapter
     */
    public function __construct(GridStack $gridStackAdapter)
    {
        $this->gridStackAdapter = $gridStackAdapter;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Widgets_Extension_Resolver';
    }

    /**
     * Create ui component view helper
     * 
     * @return Twig_SimpleFunction
     */
    protected function components()
    {
        $function = function (array $params = null) {
            try {
                $this->gridStackAdapter->scripts();

                if (!empty($params)) {
                    return app('ui-components')->findAllByResourceAndNames(uri(), $params);
                }
                return app('ui-components')->findAllByResource(uri());
            } catch (Exception $e) {
                Log::emergency($e);
                return [];
            }
        };
        return new Twig_SimpleFunction(
                'components', $function
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            $this->components()
        ];
    }

}
