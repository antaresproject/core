<?php

namespace Antares\Model;

use Antares\Extension\Config\Settings;
use Antares\Extension\Contracts\Config\SettingsContract;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseEloquent;

/**
 * Class PackageModel
 *
 * @property int $id
 * @property string $name
 * @property string $vendor
 * @property int $status
 * @property bool $required
 * @property array $options
 * @property Action[] $actions
 * @method Builder withActions()
 */
class Component extends BaseEloquent
{

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'vendor', 'name', 'status', 'options', 'required',
    ];

    /**
     * {@inheritdoc}
     */
    protected $table = 'tbl_components';

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'id'        => 'integer',
        'status'    => 'integer',
        'required' 	=> 'boolean',
        'options'   => 'array',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'status'    => ExtensionContract::STATUS_AVAILABLE,
        'required' 	=> false,
    ];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getVendor() {
        return $this->vendor;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFullName() {
        return $this->vendor . '/' . $this->name;
    }

    /**
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getOptions() : array {
        return (array) $this->options;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Action[]
     */
    public function actions() {
        return $this->hasMany(Action::class, 'component_id');
    }

    /**
     * Returns the query with related actions.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithActions(Builder $query) {
        return $query->with(Action::class);
    }

    /**
     * @param string $vendor
     * @param string $name
     * @return static|null
     */
    public static function findByVendorAndName(string $vendor, string $name) {
        return static::where('vendor', $vendor)->where('name', $name)->first();
    }

    /**
     * fetch one record by name column
     * @param String $name
     * @return Eloquent
     */
    public static function findOneByName($name)
    {
        $name = str_replace('_', '-', $name);

        if( ! Str::contains($name, '/') ) {
            $name = 'antaresproject/component-' . $name;
        }

        list($vendor, $name) = explode('/', $name);

        return static::findByVendorAndName($vendor, $name);
    }

    /**
     * Return a meta data belong to a user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAction(Builder $query, $name)
    {
        return $query->with(Action::class)->where('name', $name);
    }

}
