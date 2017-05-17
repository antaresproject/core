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

namespace Antares\Html\Support;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Traits\Macroable;
use Antares\Html\Support\Traits\InputTrait;
use Antares\Html\Support\Traits\CheckerTrait;
use Antares\Html\Support\Traits\CreatorTrait;
use Antares\Html\Support\Traits\SelectionTrait;
use Antares\Html\Support\Traits\SessionHelperTrait;

class FormBuilder
{

    use CheckerTrait,
        CreatorTrait,
        InputTrait,
        Macroable,
        SelectionTrait,
        SessionHelperTrait;

    /**
     * The HTML builder instance.
     *
     * @var \Antares\Html\Support\HtmlBuilder
     */
    protected $html;

    /**
     * Create a new form builder instance.
     *
     * @param  \Antares\Html\Support\HtmlBuilder  $html
     * @param  \Illuminate\Routing\UrlGenerator  $url
     * @param  string|null  $csrfToken
     */
    public function __construct(HtmlBuilder $html, UrlGenerator $url, $csrfToken = null)
    {
        $this->url       = $url;
        $this->html      = $html;
        $this->csrfToken = $csrfToken;
    }

    /**
     * Generate a hidden field with the current CSRF token.
     *
     * @return string
     */
    public function token()
    {
        if (empty($this->csrfToken) && !is_null($this->session)) {
            $this->csrfToken = $this->session->token();
        }

        return $this->hidden('_token', $this->csrfToken);
    }

    /**
     * Create a new model based form builder.
     *
     * @param  mixed  $model
     * @param  array  $options
     *
     * @return string
     */
    public function model($model, array $options = [])
    {
        $this->model = $model;

        return $this->open($options);
    }

    /**
     * Set the model instance on the form builder.
     *
     * @param  mixed  $model
     *
     * @return void
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Create a form label element.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     *
     * @return string
     */
    public function label($name, $value = null, $options = [])
    {
        $this->labels[] = $name;
        $options        = $this->html->attributes($options);
        $value          = e($this->formatLabel($name, $value));
        return '<label for="' . $name . '"' . $options . '>' . $value . '</label>';
    }

    /**
     * Creates form html label element
     * 
     * @param \Antares\Html\Form\Field $control
     * @param array $options
     * @return String
     */
    public function htmlLabel($control, $options = ['class' => 'form-block__name'])
    {
        $this->labels[] = $control->name;
        $value          = e($this->formatLabel($control->name, $control->label));
        $tip            = isset($control->tip) ? tooltip($control->name, $control->tip) : '';
        return '<label for="' . $control->name . '"' . $this->html->attributes($options) . '>' . $value . $tip . '</label>';
    }

    /**
     * Format the label value.
     *
     * @param  string  $name
     * @param  string|null  $value
     *
     * @return string
     */
    protected function formatLabel($name, $value)
    {
        return $value ?: ucwords(str_replace('_', ' ', $name));
    }

    /**
     * Determine if old input or model input exists for a key.
     *
     * @param  string  $name
     *
     * @return bool
     */
    protected function missingOldAndModel($name)
    {
        return (is_null($this->old($name)) && is_null($this->getModelValueAttribute($name)));
    }

    /**
     * Create a HTML reset input element.
     *
     * @param  string  $value
     * @param  array   $attributes
     *
     * @return string
     */
    public function reset($value, $attributes = [])
    {
        return $this->input('reset', null, $value, $attributes);
    }

    /**
     * Create a hidden input field.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     *
     * @return string
     */
    public function hidden($name, $value = null, $options = [])
    {
        return $this->input('hidden', $name, $value, $options);
    }

    /**
     * Create a submit button element.
     *
     * @param  string  $value
     * @param  array   $options
     *
     * @return string
     */
    public function submit($value = null, $options = [])
    {
        return $this->input('submit', null, $value, $options);
    }

    /**
     * Create a button element.
     *
     * @param  string  $value
     * @param  array   $options
     *
     * @return string
     */
    public function button($value = null, $options = [])
    {
        if (!array_key_exists('type', $options)) {
            $options['type'] = 'button';
        }
        return '<button' . $this->html->attributes($options) . '>' . $value . '</button>';
    }

    /**
     * Get the ID attribute for a field name.
     *
     * @param  string  $name
     * @param  array   $attributes
     *
     * @return string
     */
    public function getIdAttribute($name, $attributes)
    {
        if (array_key_exists('id', $attributes)) {
            return $attributes['id'];
        }

        if (in_array($name, $this->labels)) {
            return $name;
        }
    }

    /**
     * Get the value that should be assigned to the field.
     *
     * @param  string  $name
     * @param  string  $value
     *
     * @return string
     */
    public function getValueAttribute($name, $value = null)
    {
        if (is_null($name)) {
            return $value;
        }

        if (!is_null($this->old($name))) {
            return $this->old($name);
        }

        if (!is_null($value)) {
            return $value;
        }

        if (isset($this->model)) {
            return $this->getModelValueAttribute($name);
        }
    }

    /**
     * Get the model value that should be assigned to the field.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function getModelValueAttribute($name)
    {
        return data_get($this->model, $this->transformKey($name));
    }

    /**
     * Transform key from array to dot syntax.
     *
     * @param  string  $key
     *
     * @return string
     */
    protected function transformKey($key)
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }

    /**
     * Get html builder.
     *
     * @return \Antares\Html\Support\HtmlBuilder
     */
    public function getHtmlBuilder()
    {
        return $this->html;
    }

}
