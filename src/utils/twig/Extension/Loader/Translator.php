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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Twig\Extension\Loader;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

/**
 * Access Laravels translator class in your Twig templates.
 */
class Translator extends Twig_Extension
{

    /**
     * @var \Illuminate\Translation\Translator
     */
    protected $translator;

    /**
     * Create a new translator extension
     */
    public function __construct()
    {
        $this->translator = app('translator');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Translator';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('trans', function ($id, array $parameters = [], $domain = 'messages', $locale = null) {
                return $this->translator->trans($id, $parameters, $domain, $locale);
            }),
            new Twig_SimpleFunction('trans_choice', [$this->translator, 'transChoice']),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter(
                    'trans', [$this->translator, 'trans'], [
                'pre_escape' => 'html',
                'is_safe'    => ['html'],
                    ]
            ),
            new Twig_SimpleFilter(
                    'trans_choice', [$this->translator, 'transChoice'], [
                'pre_escape' => 'html',
                'is_safe'    => ['html'],
                    ]
            ),
        ];
    }

}
