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
     * Settings constructor.
     * @param array $data (optional) Settings data.
     * @param array $rules (optional) Validation rules.
     * @param array $phrases (optional) Validation phrases.
     */
    public function __construct(array $data = [], array $rules = [], array $phrases = []) {
        $this->data     = $data;
        $this->rules    = $rules;
        $this->phrases  = $phrases;
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
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() : array {
        return [
            'data'      => $this->getData(),
            'rules'     => $this->getValidationRules(),
            'phrases'   => $this->getValidationPhrases(),
        ];
    }

}
