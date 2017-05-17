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


namespace Antares\Notifier\Adapter;

use Exception;
use Illuminate\Support\Arr;
use Antares\Support\Facades\Notifier;
use Illuminate\Contracts\Support\Arrayable;
use Antares\Contracts\Notification\Recipient;
use Antares\Contracts\Notification\Message as MessageContract;

class EmailAdapter extends AbstractAdapter
{

    /**
     * sends email message
     * 
     * @param \Antares\Notifier\Adapter\Recipient $user
     * @param String $subject
     * @param \Illuminate\View\View $view
     * @param array $data
     * @return type
     */
    public function send(Recipient $user, $subject, $view = null, array $data = [])
    {
        $entity = $user;

        if ($subject instanceof MessageContract) {
            $data    = $subject->getData();
            $view    = $subject->getView();
            $subject = $subject->getSubject();
        }

        if ($user instanceof Arrayable) {
            $entity = $user->toArray();
        }

        $data = Arr::add($data, 'user', $entity);
        return Notifier::send($user, Message::create($view, $data, $subject));
    }

    /**
     * validates whether connection to gateway is established
     * 
     * @return type
     * @throws Exception
     */
    protected function validate()
    {
        $result = $this->request($action = 'CheckCredits');
        if (isset($this->config['codes'][$result])) {
            throw new Exception($this->config['codes'][$result]);
        }
        return $result;
    }

    /**
     * create request to sms gateway
     * 
     * @param String $action
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    protected function request($action = '', $params = array())
    {
        
    }

}
