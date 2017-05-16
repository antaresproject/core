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


namespace Antares\Notifications\Processor;

use Antares\Notifications\Decorator\SidebarItemDecorator;
use Antares\Notifications\Repository\StackRepository;
use Antares\Foundation\Processor\Processor;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Exception;

class SidebarProcessor extends Processor
{

    /**
     * Notifications stack repository
     *
     * @var NotificationsStack
     */
    protected $stack;

    /**
     * Sidebar item decorator;
     *
     * @var SidebarItemDecorator 
     */
    protected $decorator;

    /**
     * Construct
     * 
     * @param StackRepository $stack
     * @param SidebarItemDecorator $decorator
     */
    public function __construct(StackRepository $stack, SidebarItemDecorator $decorator)
    {
        $this->stack     = $stack;
        $this->decorator = $decorator;
    }

    /**
     * Deletes item from sidebar
     * 
     * @return JsonResponse
     */
    public function delete()
    {
        try {
            if (is_null($id = Input::get('id'))) {
                throw new Exception('Invalid notification id provided');
            }
            $this->stack->deleteById($id);
        } catch (Exception $ex) {
            Log::alert($ex);
            return new JsonResponse(['message' => trans('antares/notifications::messages.sidebar.unable_to_delete_notification_item')], 500);
        }
    }

    /**
     * Marks notification item as read
     * 
     * @return JsonResponse
     */
    public function read()
    {
        try {
            $this->stack->markAsRead(from_route('type', 'notifications'));
            return new JsonResponse('', 200);
        } catch (Exception $ex) {
            Log::alert($ex);
            return new JsonResponse(['message' => trans('antares/notifications::messages.sidebar.unable_to_mark_notifications_as_read')], 500);
        }
    }

    /**
     * Gets notifications
     * 
     * @return JsonResponse
     */
    public function get()
    {
        $notifications = $this->stack->getNotifications()->get();
        $alerts        = $this->stack->getAlerts()->get();
        $count         = $this->stack->getCount();
        $return        = [
            'notifications' => [
                'items' => $this->decorator->decorate($notifications),
                'count' => array_get($count, 'notifications', 0),
            ],
            'alerts'        => [
                'items' => $this->decorator->decorate($alerts, 'alert'),
                'count' => array_get($count, 'alerts', 0)
            ],
        ];
        return new JsonResponse($return, 200);
    }

    /**
     * Clears messages depends on type
     * 
     * @param type $type
     * @return JsonResponse
     */
    public function clear($type = null)
    {
        return new JsonResponse([$this->stack->clear($type)]);
    }

}
