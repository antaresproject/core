<?php

namespace Antares\Events\Events;

use Antares\Events\Contracts\GlobalEventInterface;
use Antares\Events\Model\Event as EventModel;

/**
 * Class AbstractEvent
 * @package Antares\Events\Events
 */
abstract class AbstractEvent implements GlobalEventInterface
{

	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var string
	 */
	public $description;
	/**
	 * @var bool
	 */
	protected $countable = false;

	/**
	 * AbstractEvent constructor.
	 * @param $params
	 */
	public function __construct($params)
	{
		if (!(is_array($params) && isset($params['showEvents'])) && $this->countable) {
			(new EventModel())->where('namespace', get_class($this))->increment('fire_count');
		}

	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}


}