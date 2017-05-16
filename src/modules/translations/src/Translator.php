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



namespace Antares\Translations;

use Illuminate\Translation\Translator as LaravelTranslator;
use Illuminate\Translation\LoaderInterface;

class Translator extends LaravelTranslator
{

    protected $isPublished = false;

    /**
     * Constructing
     * 
     * @param LoaderInterface $loader
     * @param String $locale
     */
    public function __construct(LoaderInterface $loader, $locale)
    {
        parent::__construct($loader, $locale);
        $this->isPublished = is_dir(resource_path('lang/' . area()));
        if ($this->isPublished) {
            $this->loader->replaceHints();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = [], $domain = 'messages', $locale = null, $fallback = true)
    {
        return $this->get($id, $parameters, $locale, $fallback);
    }

    /**
     * {@inheritdoc}
     */
    public function addNamespace($namespace, $hint)
    {
        if ($this->isPublished) {
            return $this->loader->addPublishedNamespace($namespace, $hint);
        }
        return parent::addNamespace($namespace, $hint);
    }

    public function transWith($id, array $parameters = [])
    {
        return $this->makeReplacements($id, $parameters);
    }

}
