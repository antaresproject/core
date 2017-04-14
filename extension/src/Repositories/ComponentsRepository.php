<?php

declare(strict_types=1);

namespace Antares\Extension\Repositories;

class ComponentsRepository {

    /**
     * @var array
     */
    protected $branches;

    /**
     * @var array
     */
    protected $required;

    /**
     * @var array
     */
    protected $optional;

    /**
     * Default branch.
     *
     * @var string
     */
    protected static $defaultBranch = 'dev-master';

    /**
     * ComponentsRepository constructor.
     * @param array $branches
     * @param array $required
     * @param array $optional
     */
    public function __construct(array $branches, array $required, array $optional) {
        $this->branches = $branches;
        $this->required = $required;
        $this->optional = $optional;
    }

    /**
     * Returns an array of components with branches.
     *
     * @return array
     */
    public function getBranches() : array {
        return $this->branches;
    }

    /**
     * Returns an array of required components with branches.
     *
     * @return array
     */
    public function getRequired() : array {
        $required = [];

        foreach($this->required as $component) {
            $required[$component] = $this->getTargetBranch($component);
        }

        return $required;
    }

    /**
     * Returns an array of optional components with branches.
     *
     * @return array
     */
    public function getOptional() : array {
        $optional = [];

        foreach($this->optional as $component) {
            $optional[$component] = $this->getTargetBranch($component);
        }

        return $optional;
    }

    /**
     * Returns the given components with branches.
     *
     * @param array $components
     * @return array
     */
    public function getWithBranches(array $components) : array {
        $data = [];

        foreach($components as $component) {
            $data[$component] = $this->getTargetBranch($component);
        }

        return $data;
    }

    /**
     * Returns the target branch.
     *
     * @param string $component
     * @return string
     */
    public function getTargetBranch(string $component) : string {
        return array_key_exists($component, $this->branches)
                ? $this->branches[$component]
                : self::$defaultBranch;
    }

    /**
     * Determines if the given component is required for the system.
     *
     * @param string $component
     * @return bool
     */
    public function isRequired(string $component) : bool {
        return in_array($component, $this->required, true);
    }

}
