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

namespace Antares\View\Notification;

use Antares\Foundation\Notification;
use Antares\Notifications\Model\Attachment;
use Antares\Support\Facades\Foundation;
use Antares\Notifications\Adapter\VariablesAdapter;
use Antares\View\Contracts\NotificationContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Twig_Loader_String;
use Twig_Environment;
use App\Jobs\Job;
use Exception;

abstract class AbstractNotificationTemplate extends Job implements NotificationContract, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

    /**
     * notification title
     *
     * @var String 
     */
    protected $title;

    /**
     * type of notification template
     *
     * @var String
     */
    protected $type = 'system';

    /**
     * notification template level
     *
     * @var String
     */
    protected $level = 'admin';

    /**
     * container with available languages
     *
     * @var array 
     */
    protected $availableLanguages = [
        'en'
    ];

    /**
     * default template language
     *
     * @var String
     */
    protected $defaultLanguage = 'en';

    /**
     * default notification category
     *
     * @var String 
     */
    protected $category = 'default';

    /**
     * notification recipients container
     *
     * @var array
     */
    protected $recipients = [];

    /**
     * notification default brands
     * 
     * @var array
     */
    protected $brands = ['default'];

    /**
     * notification predefined variables
     *
     * @var array 
     */
    public $predefinedVariables = [];

    /**
     * Available areas
     *
     * @var array
     */
    protected $areas = [];

    /**
     * Instance of notification model
     * 
     * @var Model
     */
    protected $model;

    /**
     * when notification should be sent
     *
     * @var array 
     */
    protected $events = [];

    /**
     * Attachments to notifications.
     *
     * @var array
     */
    protected $attachments = [];

    /**
     * notification template type getter
     * 
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * template title getter
     * 
     * @return boolean|String
     */
    public function getTitle()
    {
        if (isset($this->title)) {
            return $this->title;
        }
        return false;
    }

    /**
     * template default language getter
     * 
     * @return boolean|String
     */
    public function getDefaultLanguage()
    {
        if (isset($this->defaultLanguage)) {
            return $this->defaultLanguage;
        }
        return false;
    }

    /**
     * Available languages getter
     * 
     * @return boolean|array
     */
    public function getAvailableLanguages()
    {
        if (isset($this->availableLanguages)) {
            return $this->availableLanguages;
        }
        return false;
    }

    /**
     * Gets template name
     * 
     * @return String
     */
    public function getName()
    {
        return strtoupper(snake_case(str_replace(' ', '_', $this->getTitle())));
    }

    /**
     * notification category getter
     * 
     * @return String
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * notification recipients values getter
     * 
     * @return array
     */
    public function getRecipients()
    {
        if (empty($this->recipients)) {
            return [];
        }

        $recipients = ($this->recipients instanceof Collection) ? $this->recipients->toArray() : $this->recipients;

        if (!is_array($recipients)) {
            return $recipients;
        }

        $return = [];

        foreach ($recipients as $recipient) {
            if ($recipient instanceof Model) {
                if ($this->type === 'email' && ! isset($recipient->email)) {
                    continue;
                }
                if ($this->type === 'email') {
                    $return[] = $recipient->email;
                }
                if ($this->type === 'sms' && ! isset($recipient->phone)) {
                    continue;
                }
                if ($this->type === 'sms') {
                    $return[] = $recipient->phone;
                }
            }
        }
        return array_filter(array_unique($return));
    }

    /**
     * Notification recipients getter
     * 
     * @return array
     */
    public function recipients()
    {
        return $this->recipients;
    }

    /**
     * recipients
     * 
     * @param \Illuminate\Contracts\Support\Arrayable|array $recipients
     * @return AbstractNotificationTemplate
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
        return $this;
    }

    /**
     * predefined variables setter
     * 
     * @param array $variables
     * @return AbstractNotificationTemplate
     */
    public function setPredefinedVariables(array $variables = null)
    {
        $this->predefinedVariables = $variables;
        $this->setAttachments( (array) Arr::get($variables, 'attachments', []));

        return $this;
    }

    /**
     * brands getter
     * 
     * @return array
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * renders temlate view
     * 
     * @return boolean|String
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $ex) {
            Log::emergency($ex);
            return '';
        }
    }

    /**
     * get available variable instructions
     * 
     * @return array
     */
    public static function getInstructions()
    {
        return [
            'foreach' => [
                'description' => 'The foreach construct provides an easy way to iterate over arrays. foreach works only on arrays and objects, and will issue an error when you try to use it on a variable with a different data type or an uninitialized variable.',
                'instruction' => "[[foreach]]\n\t{% for element in [[list]] %}\n\t\t {{ element.attribute }}\n\t{% endfor %}\n[[/foreach]]"
            ],
            'if'      => [
                'description' => 'The if construct is one of the most important features of many languages. It allows for conditional execution of code fragments.',
                'instruction' => "[[if]]\n\t{% if [[element.attribute]] == 'foo' %} \n\t\tfoo attribute\n\t{% endif %}\n[[/if]]"
            ],
        ];
    }

    /**
     * get available variables
     * 
     * @return array
     */
    public function getVariables()
    {
        $variables  = Notification::getInstance()->all();
        $extensions = app()->make('antares.memory')->make('component')->get('extensions.active');

        if (empty($variables)) {
            return [];
        }
        $return = [];

        foreach ($variables as $extension => $config) {
            $name = ucfirst($extension === 'foundation' ? $extension : $extensions[$extension]['name']);
            $vars = (array) Arr::get($config, 'variables', []);

            if (empty($vars)) {
                continue;
            }

            foreach ($vars as $key => $variable) {
                $return[$name][] = [
                    'name'          => $key,
                    'description'   => Arr::get($variable, 'description', '')
                ];
            }
        }
        Event::fire('notifications:' . snake_case(class_basename($this)) . '.variables', [&$return]);

        return $return;
    }

    /**
     * renders template
     * 
     * @return String
     */
    public function render($view = null)
    {
        if (is_null($view) and ! isset($this->templatePaths[$this->defaultLanguage])) {
            return '';
        }
        $return   = (is_null($view)) ? view($this->templatePaths[$this->defaultLanguage], $this->predefinedVariables) : view($view, $this->predefinedVariables);
        $adapter  = app(VariablesAdapter::class);
        $rendered = $adapter->fill($return->__toString());
        preg_match_all('/\[\[(.*?)\]\]/', $rendered, $matches);
        if (isset($matches[0]) && isset($matches[1])) {
            foreach ($matches[0] as $index => $variable) {
                $rendered = str_replace($variable, '{{ ' . $matches[1][$index] . ' }}', $rendered);
            }
            $twig = new Twig_Environment(new Twig_Loader_String());
            return $twig->render($rendered, $this->predefinedVariables);
        }

        return $rendered;
    }

    /**
     * handle queue event
     * 
     * @return void
     */
    public function handle()
    {
        $handler = Foundation::make(NotificationHandler::class);
        return $handler->handle($this);
    }

    /**
     * Areas getter
     * 
     * @return array
     */
    public function getAreas()
    {
        return empty($this->areas) ? array_keys(config('areas.areas')) : $this->areas;
    }

    /**
     * Notification model setter
     * 
     * @param Model $model
     * @return \Antares\View\Notification\AbstractNotificationTemplate
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Model getter
     * 
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * gets list of events attached to notification
     * 
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Predefined variables getter
     * 
     * @return array
     */
    public function getPredefinedVariables()
    {
        return $this->predefinedVariables;
    }

    /**
     * @return VariablesAdapter
     */
    protected function getVariablesAdapter() {
        return app(VariablesAdapter::class);
    }

    /**
     * Sets attachments to the notification.
     *
     * @param array $attachments (items of Attachment class)
     */
    public function setAttachments(array $attachments) {
        $this->attachments = [];

        foreach($attachments as $attachment) {
            if($attachment instanceof Attachment) {
                $this->attachments[] = $attachment;
            }
        }
    }

    /**
     * Returns attachments.
     *
     * @return Attachment[]
     */
    public function getAttachments() : array {
        return $this->attachments;
    }

}
