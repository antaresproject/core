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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Adapter;

use Antares\UI\UIComponents\Contracts\TemplateAdapter as TemplateContract;
use Antares\UI\UIComponents\Exception\TemplateIndexNotFoundException;

class TemplateAdapter implements TemplateContract
{

    /**
     * instance of template configuration
     * 
     * @var array
     */
    protected $template = null;

    /**
     * shared template variables
     *
     * @var array 
     */
    protected $shared = [];

    /**
     * View path
     *
     * @var String 
     */
    protected $path = null;

    /**
     * construct widget implementation & fill default attributes
     */
    public function __construct($template = null)
    {
        $config = array_get(app('ui-components-templates'), $template);
        if (!is_null($config)) {
            $this->template = $config;
            $path           = array_get($config, 'path', '');
            if (!file_exists($path . DIRECTORY_SEPARATOR . 'index.twig')) {
                throw new TemplateIndexNotFoundException(sprintf('Index file not found in path: %s', $path));
            }
        } else {
            $this->path = $template;
        }
    }

    /**
     * resolve template path
     * 
     * @param String $content
     * @return String
     */
    public function decorate($content = null)
    {
        $current = realpath(__DIR__ . '/../') . '/';
        $dotted  = 'templates.default';
        $params  = array_merge($this->shared, ['content' => $content]);
        if (!is_null($this->template)) {
            $viewPath = str_replace([$current, '/resources/views/', '..'], '', $this->template['path']);
            $dotted   = str_replace(DIRECTORY_SEPARATOR, '.', $viewPath);
            return view("antares/ui-components::{$dotted}.index", array_merge($params, ['template' => $this->template['package']]))->render();
        }
        return view($this->path, $params)->render();
    }

    /**
     * share widget variables to template
     * 
     * @param array $params
     * @return TemplateAdapter
     */
    public function share(array $params = array())
    {
        $this->shared = $params;
        return $this;
    }

}
