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

namespace Antares\Messages\Extension;

use Antares\Contracts\Messages\MessageBag;
use Antares\Asset\Factory;
use Twig_SimpleFunction;
use Twig_Extension;

class FlashMessenger extends Twig_Extension
{

    /**
     * flash messenger scripts configuration
     * 
     * @var array
     */
    protected $scripts;

    /**
     * icons mapping
     * 
     * @var array
     */
    protected $mapping;

    /**
     * @var Antares\Asset\Asset 
     */
    protected $container;

    /**
     * constructing
     * @param Factory $asset
     */
    public function __construct(Factory $asset)
    {
        $config        = app('config');
        $this->scripts = $config->get('antares/messages::scripts');
        $this->mapping = $config->get('antares/messages::icon-mapping');

        $this->container = $asset->container($config->get('antares/messages::scripts.placeholder'));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Antares_Twig_Extension_FlashMessenger';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        $messages = app('antares.messages')->retrieve();

        $message = function () use($messages) {
            if (!$messages instanceof MessageBag) {
                return '';
            }
            $html = $this->checkout($messages);
            if (!$html) {
                return '';
            }
            foreach ($this->scripts['resources'] as $name => $configuration) {
                (is_array($configuration)) ?
                        $this->container->add($name, $configuration[0], $configuration[1]) :
                        $this->container->add($name, $configuration);
            }

            $this->container->inlineScript('flash-messenger', sprintf($this->inline(), $html));
        };


        return [
            new Twig_SimpleFunction('messages', $message)
        ];
    }

    /**
     * checkout messages from session container
     * 
     * @param MessageBag $messages
     * @return string
     */
    protected function checkout(MessageBag $messages)
    {
        $html       = '';
        $hasError   = false;
        $hasSuccess = false;

        foreach (['error', 'info', 'success', 'warning'] as $key) {
            if ($messages->has($key)) {
                $hasError   = (!$hasError && $key == 'error');
                $hasSuccess = (!$hasSuccess && $key == 'success');

                $flashMessages = $messages->setFormat('<div class="activity-item"> <i class="fa ' . $this->mapping[$key] . '"></i> <div class="activity">:message</div>')->get($key);


                array_walk($flashMessages, function($current) use ($key, &$html) {
                    $html .= "generate('{$key}', '{$current}');\n";
                });
            }
        }
        return $html;
    }

    /**
     * generate flash messanger inline scripts
     * 
     * @return String
     */
    protected function inline()
    {

        $inline = <<<EOD
           $(document).ready(function(){                 
                function generateAll() {
                    %s
               }
               setTimeout(function() {
                    generateAll();
                }, 500);
            

        });
EOD;

        return $inline;
    }

}
