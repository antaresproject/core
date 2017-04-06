<?php

declare(strict_types=1);

namespace Antares\Extension\Contracts\Config;

use Antares\Contracts\Html\Form\Fieldset;

interface SettingsFormContract {

    /**
     * Builds the content of the settings form.
     *
     * @param Fieldset $fieldset
     * @param SettingsContract $settings
     * @return mixed
     */
    public function build(Fieldset $fieldset, SettingsContract $settings);

}
