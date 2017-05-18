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


namespace Antares\Notifier\Message;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SmsMessage
{

    /**
     * message subject
     *
     * @var String
     */
    protected $subject = '';

    /**
     * message recipient
     *
     * @var String
     */
    protected $to;

    /**
     * message content
     *
     * @var View|String
     */
    protected $content = '';

    /**
     * subject setter
     * 
     * @param String $subject
     * @return SmsMessage
     */
    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * recipient setter
     * 
     * @param String $to
     * @return SmsMessage
     */
    public function to($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * message content setter
     * 
     * @param View $view
     * @param array $data
     * @return SmsMessage
     */
    public function content($view, array $data = array())
    {
        $this->content = htmlspecialchars_decode(($view instanceof View) ? $view->render($view, $data) : $view);
        return $this;
    }

    /**
     * validates message
     * 
     * @return boolean
     * @throws Exception
     */
    public function validate()
    {
        try {
            if (strlen($this->content) <= 0) {
                throw new Exception('Invalid sms message content.');
            }
            return true;
        } catch (Exception $ex) {
            Log::emergency($ex);
            return false;
        }
    }

    /**
     * recipient getter
     * 
     * @return String
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * content getter
     * 
     * @return String
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * subject getter
     * 
     * @return String
     */
    public function getSubject()
    {
        return $this->subject;
    }

}
