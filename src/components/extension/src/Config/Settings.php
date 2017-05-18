<?php

declare(strict_types=1);

namespace Antares\Extension\Config;

use Antares\Extension\Contracts\Config\SettingsContract;
use Illuminate\Support\Arr;

class Settings implements SettingsContract {

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $phrases = [];

    /**
     * @var string
     */
    protected $customUrl;

    /**
     * Settings constructor.
     * @param array $data
     * @param array $rules
     * @param array $phrases
     * @param string $customUrl
     */
    public function __construct(array $data = [], array $rules = [], array $phrases = [], string $customUrl = '') {
        $this->data         = $data;
        $this->rules        = $rules;
        $this->phrases      = $phrases;
        $this->customUrl    = $customUrl;
    }

    /**
     * Returns data as array.
     *
     * @return array
     */
    public function getData() : array {
        return $this->data;
    }

    /**
     * Updates the settings data by the given array.
     *
     * @param array $data
     * @return void
     */
    public function updateData(array $data) {
        foreach($data as $name => $value) {
            $this->data[$name] = $value;
        }
    }

    /**
     * Determines if there are any data.
     *
     * @return bool
     */
    public function hasData() : bool {
        return count($this->data) > 0;
    }

    /**
     * Returns a value of the given name.
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getValueByName(string $name, $default = null) {
        return Arr::get($this->data, $name, $default);
    }

    /**
     * Returns validation rules as array.
     *
     * @return array
     */
    public function getValidationRules() : array {
        return $this->rules;
    }

    /**
     * Returns validation phrases as array.
     *
     * @return array
     */
    public function getValidationPhrases() : array {
        return $this->phrases;
    }

    /**
     * Returns custom URL for settings.
     *
     * @return string
     */
    public function getCustomUrl() : string {
        return $this->customUrl;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() : array {
        return [
            'data'          => $this->getData(),
            'rules'         => $this->getValidationRules(),
            'phrases'       => $this->getValidationPhrases(),
            'custom_url'    => $this->getCustomUrl(),
        ];
    }

}
