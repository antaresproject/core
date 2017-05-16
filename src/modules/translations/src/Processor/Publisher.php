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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Translations\Processor;

use Illuminate\Translation\Translator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class Publisher
{

    /**
     * hints collection
     *
     * @var array
     */
    protected $hints;

    /**
     * Filesystem instance
     *
     * @var Filesystem 
     */
    protected $filesystem;

    /**
     * constructing
     * 
     * @param Translator $translator
     * @param Filesystem $filesystem
     */
    public function __construct(Translator $translator, Filesystem $filesystem)
    {
        $this->hints      = $translator->getLoader()->getHints();
        $this->filesystem = $filesystem;
    }

    /**
     * publish translations from database
     * 
     * @param \Illuminate\Database\Eloquent\Model $language
     * @return boolean
     */
    public function publish($language)
    {
        $translations = $this->getTranslationGroups($language->translations);
        foreach ($translations as $area => $groups) {
            foreach ($groups as $group => $items) {
                if (!isset($this->hints[$group])) {
                    continue;
                }
                $path = $this->getResourcePath($language->code, $area, $group);
                $this->publishTranslations($path, $items);
            }
            $this->putGitignore($area);
        }
        return true;
    }

    /**
     * Puts gitignore file in lang directory
     * 
     * @param String $area
     * @return boolean
     */
    protected function putGitignore($area)
    {
        $target = resource_path(implode(DIRECTORY_SEPARATOR, ['lang', $area, '.gitignore']));
        if (!file_exists($target)) {
            return $this->filesystem->put($target, "*\n!.gitignore");
        }
        return true;
    }

    /**
     * Gets resource path
     * 
     * @param String $locale
     * @param String $area
     * @param String $group
     * @return String
     */
    protected function getResourcePath($locale, $area, $group)
    {
        $path = resource_path(implode(DIRECTORY_SEPARATOR, ['lang', $area, $group, $locale]));
        if ($this->filesystem->exists($path)) {
            $this->filesystem->cleanDirectory($path);
        } else {
            $this->filesystem->makeDirectory($path, 0755, true);
        }
        return $path;
    }

    /**
     * writing translations to files
     * 
     * @param String $path
     * @param array $items
     * @return boolean
     */
    protected function publishTranslations($path, array $items)
    {
        foreach ($items as $filename => $translations) {

            $content = "<?php

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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

\n return\n\n " . var_export($translations, true) . ";\n";
            file_put_contents($path . DIRECTORY_SEPARATOR . $filename . '.php', $content);
        }
        return true;
    }

    /**
     * grouping translations by component name
     * 
     * @param Collection $list
     * @return array
     */
    protected function getTranslationGroups(Collection $list)
    {
        $groups = [];
        foreach ($list as $model) {
            if (!isset($groups[$model->area][$model->group])) {
                $groups[$model->area][$model->group] = [];
            }
            $groups[$model->area][$model->group] = Arr::add($groups[$model->area][$model->group], $model->key, $model->value);
        }
        return $groups;
    }

}
