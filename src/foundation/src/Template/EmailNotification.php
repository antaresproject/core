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

namespace Antares\Foundation\Template;

use Antares\View\Notification\AbstractNotificationTemplate;
use Antares\Notifications\Adapter\VariablesAdapter;
use Antares\Notifications\Model\NotificationsStack;
use Antares\Brands\Model\BrandOptions;
use Illuminate\Support\Facades\Log;
use Twig_Loader_String;
use Twig_Environment;
use Exception;

class EmailNotification extends AbstractNotificationTemplate implements SendableNotification
{

    /**
     * type of notification template
     *
     * @var String
     */
    protected $type = 'email';

    /**
     * notification events
     *
     * @var array 
     */
    protected $events = [
        'antares.notifier.events.custom'
    ];

    /**
     * notification category
     *
     * @var type 
     */
    protected $category = 'default';

    /**
     * Gets title
     * 
     * @return String
     */
    public function getTitle()
    {
        $title = array_get($this->getModel(), 'contents.0.title');
        $twig  = new Twig_Environment(new Twig_Loader_String());
        return $twig->render($title, (array) $this->predefinedVariables);
    }

    /**
     * renders template
     * 
     * @return String
     */
    public function render($view = null)
    {

        $model         = $this->getModel();
        $view          = app(VariablesAdapter::class)->setVariables((array) $this->predefinedVariables)->get(array_get($model, 'contents.0.content', array_get($model, 'content.0.content')));
        $brandTemplate = BrandOptions::query()->where('brand_id', brand_id())->first();
        $header        = str_replace('</head>', '<style>' . $brandTemplate->styles . '</style></head>', $brandTemplate->header);
        return preg_replace("/<body[^>]*>(.*?)<\/body>/is", '<body>' . $view . '</body>', $header . $brandTemplate->footer);
    }

    /**
     * Handles email notification
     */
    public function handle()
    {

        try {
            if (empty($this->recipients) && !is_null($recipients = array_get($this->predefinedVariables, 'recipients'))) {
                $this->recipients = $recipients;
            }

            $result = parent::handle();
            $code   = $result->getResultCode();

            $model = $this->getModel();
            $stack = new NotificationsStack([
                'notification_id' => array_get($model, 'id'),
                'author_id'       => auth()->guest() ? null : user()->id,
                'variables'       => array_merge($this->predefinedVariables, ['recipients' => $this->recipients]),
            ]);
            $stack->save();
        } catch (Exception $ex) {
            Log::error($ex);
            $code = 0;
        }
        return $code;
    }

}
