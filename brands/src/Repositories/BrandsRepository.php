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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Brands\Repositories;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Antares\Brands\Contracts\BrandsRepositoryContract;
use Antares\Translations\Models\Languages;
use Illuminate\Database\Eloquent\Builder;
use Antares\Brands\Model\Brands as Model;
use Illuminate\Support\Facades\Log;
use Antares\Brands\Model\Country;
use Exception;

class BrandsRepository implements BrandsRepositoryContract
{

    /**
     * repository model instance
     *
     * @var Model
     */
    protected $model;

    /**
     * constructing
     * 
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * finds all rows
     * 
     * @param array $columns
     * @return Builder
     */
    public function findAll($columns = ['*'])
    {
        return $this->model->newQuery()->with(['options', 'templates' => function($query) {
                        $query->where('area', area());
                    }])->select($columns);
    }

    /**
     * finds row by identifier
     * 
     * @param mixed $id
     * @return Model | null
     */
    public function findById($id)
    {
        return $this->model->newQuery()->with('options')->findOrFail($id);
    }

    /**
     * finds default brand
     * 
     * @return Model | null
     */
    public function findDefault()
    {
        return $this->model->newQuery()->where('default', 1)->first();
    }

    /**
     * add new brand into database
     * 
     * @param Model $brand
     * @param array $data
     * @throws Exception
     */
    public function store(Model $brand, array $data)
    {
        $connection = $this->model->getConnection();
        $connection->beginTransaction();

        try {
            $currentDefault = $this->findDefault();

            if (!$currentDefault) {
                $data['default'] = 1;
            }
            $data['status'] = 1;
            $brand->fill($data)->save();

            if (isset($data['import'])) {
                $importBrandId = array_get($data, 'brands');
                $fromModel     = $this->findById($importBrandId) ? : $currentDefault;
                $this->copyPermissions($fromModel, $brand);
            }
            $this->saveOptions($brand, $data);
            $this->saveTemplates($brand, $data);
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    /**
     * copy permsission from selected brand
     * 
     * @param Model $fromModel
     * @param Model $targetModel
     */
    protected function copyPermissions(Model $fromModel, Model $targetModel)
    {
        $permissions = $fromModel->permissions->toArray();

        $inserts = array_map(function($value) use($targetModel) {
            unset($value['id']);
            $value['brand_id'] = $targetModel->id;
            return $value;
        }, $permissions);

        $this->model->getConnection()->table('tbl_permissions')->insert($inserts);
    }

    /**
     * updates brand
     * 
     * @param Model $brand
     * @param int $id
     * @param array $data
     * @throws Exception
     */
    public function update(Model $brand, array $data)
    {
        $connection = $this->model->getConnection();
        $connection->beginTransaction();
        try {
            $currentDefault = $this->findDefault();
            if ($currentDefault AND array_get($data, 'default') AND $currentDefault->id !== $brand->id) {
                $currentDefault->default = false;
                $currentDefault->save();
            }
            $brand->fill($data)->save();
            $this->saveOptions($brand, $data);
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    /**
     * saves brand options
     * 
     * @param EloquentModel $brand
     * @param array $data
     * @return boolean
     */
    protected function saveTemplates(EloquentModel $brand, array $data = array())
    {
        try {
            $area   = array_get($data, 'area');
            $config = config('antares/brands::install.default');
            if (!$area) {
                $areas = config('areas.areas');
                $data  = array_merge($data, $config);
                foreach ($areas as $name => $title) {
                    $this->saveTemplate($brand, $name, $data);
                }
            } else {
                $this->saveTemplate($brand, $area, $data);
            }
            return true;
        } catch (Exception $ex) {
            Log::alert($ex);
        }
        return false;
    }

    /**
     * Saves template
     * 
     * @param EloquentModel $brand
     * @param String $area
     * @param array $data
     * @return boolean
     */
    public function saveTemplate(EloquentModel $brand, $area, array $data)
    {
        $template = $brand->templates()->getModel()->query()->firstOrNew(['brand_id' => $brand->id, 'area' => $area]);
        $template->fill($data);
        return $template->save();
    }

    /**
     * Saves brand options
     * 
     * @param String $area
     * @param EloquentModel $brand
     * @param array $data
     * @return EloquentModel
     */
    private function saveOptions(EloquentModel $brand, array $data = array())
    {
        $options  = $brand->options()->getModel()->query()->firstOrNew(['brand_id' => $brand->id]);
        $language = app(Languages::class)->query()->where('code', $data['default_language'])->firstOrFail();
        $country  = app(Country::class)->query()->where('code', $data['default_country'])->firstOrFail();
        if (!isset($data['header'])) {
            $data['header'] = config('antares/brands::default.header')->render();
        }
        if (!isset($data['styles'])) {
            $data['styles'] = config('antares/brands::default.styles')->render();
        }
        if (!isset($data['footer'])) {
            $data['footer'] = config('antares/brands::default.footer')->render();
        }
        $options->fill([
            'country_id'     => $country->id,
            'language_id'    => $language->id,
            'date_format_id' => $data['date_format'],
            'maintenance'    => isset($data['maintenance_mode']) && $data['maintenance_mode'] == 'on' ? 1 : 0] + $data);

        return $brand->options()->save($options);
    }

    /**
     * deletes brand by model
     * 
     * @param Model $brand
     */
    public function delete(Model $brand)
    {
        return $brand->delete();
    }

    /**
     * sets default brand by brand id
     * 
     * @param int $id
     * @throws Exception
     */
    public function setDefaultBrandById($id)
    {
        $connection = $this->model->getConnection();
        $connection->beginTransaction();

        try {
            $connection->table('tbl_brands')->update(['default' => 0]);

            $this->findById($id)->update(['default' => 1]);

            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    /**
     * saves brand email templates
     * 
     * @param EloquentModel $model
     * @param array $data
     * @return EloquentModel
     */
    public function storeTemplate(EloquentModel $model, array $data = [])
    {
        $template = $model->options()->firstOrNew(['brand_id' => $model->id]);
        $template->fill($data);
        return $template->save();
    }

    /**
     * Finds brand with template
     * 
     * @param mixed $brandId
     * @param mixed $templateId
     * @return Model
     */
    public function findByIdAndTemplate($brandId, $templateId)
    {
        return $this->model->newQuery()->whereId($brandId)->with(['templates' => function($query) use($templateId) {
                        $query->whereId($templateId);
                    }])->firstOrFail();
    }

}
