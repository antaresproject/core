<?php

declare(strict_types=1);

namespace Antares\Installation\Repository;

use Illuminate\Support\Arr;
use File;

class Installation {

    /**
     * Installation instance name.
     *
     * @var string
     */
    protected $name = 'installation';

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Installation constructor.
     */
    public function __construct() {
        $this->filePath = storage_path('installation-config.txt');

        if( ! File::exists($this->filePath) ) {
            File::put($this->filePath, serialize([]));
        }
        else {
            $this->attributes = $this->readFromFile();
        }
    }

    /**
     * @return bool
     */
    public function started() : bool {
        return (bool) $this->getCustom('started', false);
    }

    /**
     * @param bool $state
     * @return void
     */
    public function setStarted(bool $state) {
        $this->setCustom('started', $state);
    }

    /**
     * @param int $steps
     * @return void
     */
    public function setSteps(int $steps) {
        $this->setCustom('steps', $steps);
    }

    /**
     * @return int
     */
    public function getSteps() : int {
        return (int) $this->getCustom('steps', 0);
    }

    /**
     * @param int $steps
     * @return void
     */
    public function setCompletedSteps(int $steps) {
        $this->setCustom('completed_steps', $steps);
    }

    /**
     * @return int
     */
    public function getCompletedSteps() : int {
        return (int) $this->getCustom('completed_steps', 0);
    }

    /**
     * @return void
     */
    public function forgetStepsInfo() {
        $this->forgetCustom('steps');
        $this->forgetCustom('completed_steps');
    }

    /**
     * @return bool
     */
    public function finished() : bool {
        return $this->started() && $this->getCompletedSteps() >= $this->getSteps();
    }

    /**
     * @return int|null
     */
    public function getPid() {
        return $this->getCustom('pid');
    }

    /**
     * @param int|null $pid
     * @return void
     */
    public function setPid(int $pid = null) {
        if($pid === null) {
            $this->forgetCustom('pid');
        }
        else {
            $this->setCustom('pid', $pid);
        }
    }

    /**
     * @return bool
     */
    public function progressing() : bool {
        return $this->started() && ! $this->finished() && ! $this->failed();
    }

    /**
     * @return bool
     */
    public function failed() : bool {
        return (bool) $this->getCustom('failed', false);
    }

    /**
     * @param bool $state
     * @return void
     */
    public function setFailed(bool $state) {
        $this->setCustom('failed', $state);
    }

    /**
     * @return string
     */
    public function getFailedMessage() : string {
        return (string) $this->getCustom('failed_message', '');
    }

    /**
     * @param string $message
     * @return void
     */
    public function setFailedMessage(string $message) {
        $this->setCustom('failed_message', $message);
    }

    /**
     * @return string
     */
    public function getSuccessMessage() : string {
        return (string) $this->getCustom('success_message', '');
    }

    /**
     * @param string $message
     * @return void
     */
    public function setSuccessMessage(string $message) {
        $this->setCustom('success_message', $message);
    }

    /**
     * Sets custom data.
     *
     * @param string $key
     * @param $data
     */
    public function setCustom(string $key, $data) {
        Arr::set($this->attributes, $this->getKey($key), $data);
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getCustom(string $key, $default = null) {
        return Arr::get($this->attributes, $this->getKey($key), $default);
    }

    /**
     * Forget custom key.
     *
     * @param string $key
     */
    public function forgetCustom(string $key) {
        Arr::forget($this->attributes, $this->getKey($key));
    }

    /**
     * Forgets all data from the installation.
     *
     * @return void
     */
    public function forget() {
        Arr::forget($this->attributes, $this->name);
    }

    /**
     * Returns all data stored in the installation.
     *
     * @return array
     */
    public function all() {
        return Arr::get($this->attributes, $this->name, []);
    }

    /**
     * @param string $key
     * @return string
     */
    private function getKey(string $key) : string {
        return $this->name . '.' . $key;
    }

    /**
     * @return array
     */
    private function readFromFile() : array {
        $data = File::get($this->filePath, true);

        if($data) {
            $data = unserialize($data, ['allowed_classes' => false]);

            if ($data !== false && $data !== null && is_array($data)) {
                return $data;
            }
        }

        return [];
    }

    public function save() {
        \Log::info('file', $this->attributes);

        File::put($this->filePath, serialize($this->attributes));
    }

}
