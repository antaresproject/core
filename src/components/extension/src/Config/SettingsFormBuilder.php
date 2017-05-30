<?php

declare(strict_types = 1);

namespace Antares\Extension\Config;

use Antares\Extension\Contracts\Config\SettingsContract;
use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Extension\Contracts\Config\SettingsFormContract;
use Antares\Html\Form\Fieldset;
use Antares\Html\Form\Grid as FormGrid;
use Antares\Extension\Model\ExtensionModel;

class SettingsFormBuilder
{

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * SettingsForm constructor.
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Builds the form for the given extension model and its settings.
     *
     * @param ExtensionModel $model
     * @param SettingsFormContract $settingsForm
     * @param SettingsContract $settings
     * @return \Antares\Contracts\Html\Builder
     */
    public function build(ExtensionModel $model, SettingsFormContract $settingsForm, SettingsContract $settings)
    {
        return $this->formFactory->make(function(FormGrid $form) use($model, $settingsForm, $settings) {
                    $url = route(area() . '.modules.viewer.configuration.update', ['id' => $model->getId()]);

                    $form->simple($url, [], $model);
                    $form->name($model->getFullName() . ' Configuration');
                    $form->hidden('id');
                    $form->layout('antares/foundation::components.form');

                    $form->fieldset('Configuration', function(Fieldset $fieldset) use($settingsForm, $settings) {
                        $settingsForm->build($fieldset, $settings);

                        $fieldset->control('button', 'button')
                                ->attributes(['type' => 'submit', 'class' => 'btn btn-primary'])
                                ->value(trans('antares/foundation::label.save_changes'));
                    });

                    $form
                            ->rules($settings->getValidationRules())
                            ->phrases($settings->getValidationPhrases());
                });
    }

}
