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


namespace Antares\Pagination;

use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Contracts\Pagination\Presenter as PresenterContract;
use Illuminate\Pagination\BootstrapThreeNextPreviousButtonRendererTrait;
use Illuminate\Pagination\UrlWindowPresenterTrait;

class PaginationAjaxPresenter implements PresenterContract
{

    use BootstrapThreeNextPreviousButtonRendererTrait,
        UrlWindowPresenterTrait;

    /**
     * The paginator implementation.
     *
     * @var \Illuminate\Contracts\Pagination\Paginator
     */
    protected $paginator;

    /**
     * The URL window data structure.
     *
     * @var array
     */
    protected $window;

    /**
     * Per page scale default definition
     *
     * @var array
     */
    protected $perPageScale = [10, 20, 30, 50];

    /**
     * Create a new Bootstrap presenter instance.
     *
     * @param  \Illuminate\Contracts\Pagination\Paginator  $paginator
     * @param  \Illuminate\Pagination\UrlWindow|null  $window
     * @return void
     */
    public function __construct(PaginatorContract $paginator, UrlWindow $window = null)
    {
        if (!empty($query = app('request')->query())) {
            $paginator->appends($query);
        }

        $this->paginator = $paginator;
        $this->window    = is_null($window) ? UrlWindow::make($paginator) : $window->get();
    }

    /**
     * Determine if the underlying paginator being presented has pages to show.
     *
     * @return bool
     */
    public function hasPages()
    {
        return $this->paginator->hasPages();
    }

    /**
     * Convert the URL window into Bootstrap HTML.
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function render()
    {

        $hasMorePages = $this->paginator->hasMorePages();
        $hasPages     = $this->paginator->hasPages();
        $total        = count($this->paginator->items());
        if (!$hasMorePages && !$hasPages && !$total) {
            return '';
        }

        if ($this->currentPage() == 1 && $total < 10) {
            return '';
        }

        return view('antares/foundation::layouts.antares.partials.pagination._pagination', [
                    'previousButton' => strip_tags($this->getPreviousButton('<i class="zmdi zmdi-chevron-left"></i>'), '<a><i>'),
                    'links'          => $this->getLinks(),
                    'nextButton'     => strip_tags($this->getNextButton('<i class="zmdi zmdi-chevron-right"></i>'), '<a><i>'),
                    'perPage'        => $this->paginator->perPage(),
                    'url'            => $this->paginator->url($this->paginator->currentPage()),
                    'perPageUrl'     => $this->paginator->url(1),
                    'perPageScale'   => $this->perPageScale
                ])->render();
    }

    /**
     * Get HTML wrapper for an available page link.
     *
     * @param  string  $url
     * @param  int  $page
     * @param  string|null  $rel
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page, $rel = null)
    {
        $rel = is_null($rel) ? '' : ' rel="' . $rel . '"';
        return '<li><a class="mdl-js-button mdl-js-ripple-effect ajaxable pagination-ajax" href="' . htmlentities($url) . '"' . $rel . '>' . $page . '</a></li>';
    }

    /**
     * Get HTML wrapper for disabled text.
     *
     * @param  string  $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<a href="#" class="mdl-js-button mdl-js-ripple-effect">' . $text . '</a>';
    }

    /**
     * Get HTML wrapper for active text.
     *
     * @param  string  $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<li class="pagination-pages__sgl--active"><a class="mdl-js-button mdl-js-ripple-effect" href="#">' . $text . '</a></li>';
    }

    /**
     * Get a pagination "dot" element.
     *
     * @return string
     */
    protected function getDots()
    {
        return '<li>' . $this->getDisabledTextWrapper('...') . '</li>';
    }

    /**
     * Get the current page from the paginator.
     *
     * @return int
     */
    protected function currentPage()
    {
        return $this->paginator->currentPage();
    }

    /**
     * Get the last page from the paginator.
     *
     * @return int
     */
    protected function lastPage()
    {
        return $this->paginator->lastPage();
    }

    /**
     * Add a query string value to the paginator.
     *
     * @param  string  $key
     * @param  string  $value
     * @return \Antares\Pagination\PaginationAjaxPresenter
     */
    public function addQuery($key, $value)
    {
        $this->paginator->addQuery($key, $value);
        $this->window = UrlWindow::make($this->paginator);
        return $this;
    }

    /**
     * Per page scale setter
     * 
     * @param array $perPageScale
     * @return \Antares\Pagination\PaginationAjaxPresenter
     */
    public function setPerPageScale(array $perPageScale)
    {
        $this->perPageScale = $perPageScale;
        return $this;
    }

}
