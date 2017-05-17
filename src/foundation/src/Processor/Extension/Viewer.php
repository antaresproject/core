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

namespace Antares\Foundation\Processor\Extension;

use Antares\Extension\Model\ExtensionModel;
use Antares\Extension\SettingsFormResolver;
use Antares\Foundation\Http\Controllers\Extension\ViewerController;
use Antares\Foundation\Processor\Processor;
use Antares\Contracts\Extension\Command\Viewer as Command;
use Antares\Foundation\Http\Presenters\Extension as Presenter;
use Antares\Extension\Manager;
use Validator;
use Log;

class Viewer extends Processor implements Command
{

    /**
     * The presenter implementation.
     *
     * @var Presenter
     */
    protected $presenter;

    /**
     * @var SettingsFormResolver
     */
    protected $settingsFormResolver;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * Viewer constructor.
     * @param Presenter $presenter
     * @param SettingsFormResolver $settingsFormResolver
     * @param Manager $manager
     */
    public function __construct(Presenter $presenter, SettingsFormResolver $settingsFormResolver, Manager $manager) {
        $this->presenter            = $presenter;
        $this->settingsFormResolver = $settingsFormResolver;
        $this->manager              = $manager;
    }

    /**
     * View all extension page.
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        return $this->presenter->table();
    }

    /**
     * @param ViewerController $handler
     * @param string $vendor
     * @param string $name
     * @return \Illuminate\Contracts\View\View|mixed
     */
    public function showConfigurationForm(ViewerController $handler, string $vendor, string $name) {
        $component      = ExtensionModel::findByVendorAndName($vendor, $name);
        $extension      = $this->manager->getExtensionByVendorAndName($vendor, $name);

        if($component && $extension) {
            $customUrl = $extension->getSettings()->getCustomUrl();

            if($customUrl) {
                return $handler->redirectToCustomUrl($customUrl);
            }

            $settingsForm   = $this->settingsFormResolver->tryGetSettingsForm($extension);
            $form           = $this->presenter->form($component, $settingsForm, $extension->getSettings());

            return $handler->showConfigurationForm($form);
        }

        return $handler->abortWhenRequirementMismatched();
    }

    /**
     * @param ViewerController $handler
     * @param $componentId
     * @param array $options
     * @return mixed
     */
    public function updateConfiguration(ViewerController $handler, $componentId, array $options) {
        $component = ExtensionModel::find($componentId);

        if($component instanceof ExtensionModel) {
            try {
                $extension      = $this->manager->getExtensionByVendorAndName($component->vendor, $component->name);
                $settings       = $extension->getSettings();
                $options        = array_only($options, array_keys($settings->getData()));
                $validator      = Validator::make($options, $settings->getValidationRules(), $settings->getValidationPhrases());

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag()->getMessages();
                    return $handler->updateConfigurationValidationFailed($messages);
                }

                $component->options = $options;
                $component->save();

                return $handler->updateConfigurationSuccess();
            }
            catch(\Exception $e) {
                Log::critical($e->getMessage());
                return $handler->updateConfigurationFailed(['error' => $e->getMessage()]);
            }

        }

        return $handler->abortWhenRequirementMismatched();
    }

}
