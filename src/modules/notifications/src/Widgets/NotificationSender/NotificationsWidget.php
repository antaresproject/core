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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Widgets\NotificationSender;

use Antares\Notifications\Widgets\NotificationSender\Controller\NotificationController;
use Antares\Notifications\Widgets\NotificationSender\Form\NotificationWidgetForm;
use Antares\UI\UIComponents\Adapter\AbstractTemplate;
use Illuminate\Support\Facades\Route;

class NotificationsWidget extends AbstractTemplate
{

    /**
     * name of widget
     * 
     * @var String 
     */
    public $name = 'Notifications Widget';

    /**
     * Widget title at top bar
     *
     * @var String 
     */
    protected $title = 'Send notification';

    /**
     * Form instance
     *
     * @var NotificationWidgetForm 
     */
    protected $form;

    /**
     * widget attributes
     *
     * @var array
     */
    protected $attributes = [
        'x'              => 0,
        'y'              => 0,
        'min_width'      => 2,
        'min_height'     => 3,
        'max_width'      => 12,
        'max_height'     => 9,
        'default_width'  => 3,
        'default_height' => 9,
        'titlable'       => true,
    ];

    /**
     * Construct
     * 
     * @param NotificationWidgetForm $form
     */
    public function __construct(NotificationWidgetForm $form)
    {
        parent::__construct();
        $this->form = $form;
    }

    /**
     * Widgets routes implementations
     * 
     * @return void
     */
    public static function routes()
    {
        $area = area();
        Route::post($area . '/notifications/notifications', NotificationController::class . '@index');
        Route::post($area . '/notifications/widgets/send', NotificationController::class . '@send');
    }

    /**
     * Renders widget content
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        app('antares.asset')->container('antares/foundation::application')->add('webpack_forms_basic', '/webpack/forms_basic.js', ['app_cache']);

        publish('notifications', ['js/notification-widget.js']);
        return view('antares/notifications::widgets.send_notification', ['form' => $this->form->get()])->render();
    }

}
