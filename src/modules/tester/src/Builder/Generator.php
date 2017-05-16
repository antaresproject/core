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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Builder;

use Antares\Tester\Contracts\Extractor;
use Antares\Tester\Contracts\ClassValidator;
use Antares\Tester\Contracts\Builder as BuilderContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Fluent;
use Antares\Tester\Exception;

class Generator extends Builder implements BuilderContract
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Antares\Tester\Contracts\Extractor
     */
    protected $extractor;

    /**
     * @var \Antares\Tester\Contracts\ClassValidator
     */
    protected $validator;

    /**
     * constructing
     * 
     * @param Extractor $extractor
     * @param ClassValidator $validator
     */
    public function __construct(Extractor $extractor, ClassValidator $validator)
    {
        $this->config    = app('config')->get('antares/tester::config');
        $this->validator = $validator;
        $this->extractor = $extractor;
    }

    /**
     * build attributes
     * 
     * @param Fluent $field
     * @return array
     */
    protected function attributes(Fluent $field)
    {
        if (!app('antares.extension')->isActive('tester')) {
            app('antares.messages')->add('error', trans('Package tester not active. Please install package first.'));
            $previous = app('Illuminate\Routing\UrlGenerator')->previous();
            $response = new RedirectResponse($previous, 302);
            return $response->send();
        }
        $attributes = array_merge(['value' => $field->get('value'), 'name' => $field->get('name')], $field->get('attributes'));
        if (!isset($attributes['class'])) {
            $attributes['class'] = 'btn btn--md btn--indigo mdl-button mdl-js-button mdl-js-ripple-effect';
        }
        if (!isset($attributes['id'])) {
            $attributes['id'] = $this->config['inputId'];
        }
        return $attributes;
    }

    /**
     * builds form element container
     * 
     * @param String $name
     * @param array $attributes
     * @param \Antares\Tester\Factory\Closure $callback
     * @throws Exception\InvalidAttributesException
     */
    public function build($name = null, array $attributes = [], $callback = null)
    {
        if (!$this->validator->isValid($attributes)) {
            throw new Exception\InvalidAttributesException('Invalid validator class');
        }
        unset($attributes['form']);

        $field = new Fluent([
            'name'       => $name,
            'value'      => isset($attributes['value']) ? $attributes['value'] : '',
            'attributes' => $attributes,
        ]);

        if ($callback instanceof Closure) {
            call_user_func($callback, $field);
        }

        $params  = $this->attributes($field);
        $this->extractor->generateScripts($params);
        $this->memorize($field);
        $hiddens = [
            app('form')->hidden('validator', $attributes['validator'])
        ];

        return view($this->config['view'], ['hiddens' => $hiddens, 'element' => app('form')->button($attributes['title'], $params)])->render();
    }

    /**
     * insert properties into memory
     * 
     * @param Fluent $field
     */
    protected function memorize(Fluent $field)
    {
        if (!isset($field->attributes['executor'])) {
            return false;
        }
        $component  = $this->extractor->extractForm($field->attributes['executor']);
        $attributes = array_merge($component, ['name' => $field->get('name')], $field->attributes);

        $memory = app('antares.memory')->make('tests');
        $memory->put($attributes['title'], $attributes);
        $memory->finish();
    }

}
