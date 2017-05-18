<?php

declare(strict_types=1);

namespace Antares\Extension\Contracts\Config;

use Illuminate\Contracts\Support\Arrayable;

interface SettingsContract extends Arrayable {

    /**
     * Returns data as array.
     *
     * @return array
     */
    public function getData() : array;

    /**
     * Determines if there are any data.
     *
     * @return bool
     */
    public function hasData() : bool;

    /**
     * Updates the settings data by the given array.
     *
     * @param array $data
     * @return void
     */
    public function updateData(array $data);

    /**
     * Returns a value of the given name.
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getValueByName(string $name, $default = null);

    /**
     * Returns validation rules as array.
     *
     * @return array
     */
    public function getValidationRules() : array;

    /**
     * Returns validation phrases as array.
     *
     * @return array
     */
    public function getValidationPhrases() : array;

    /**
     * Returns custom URL for settings.
     *
     * @return string
     */
    public function getCustomUrl() : string;

}
