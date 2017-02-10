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

use Illuminate\Contracts\Pagination\LengthAwarePaginator as PaginatorContract;

class UrlWindow extends \Illuminate\Pagination\UrlWindow
{

    /**
     * Create a new URL window instance.
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator  $paginator
     * @param  int  $onEachSide
     * @return array
     */
    public static function make(PaginatorContract $paginator, $onEachSide = 1)
    {
        return (new static($paginator))->get($onEachSide);
    }

    /**
     * Get the window of URLs to be shown.
     *
     * @param  int  $onEachSide
     * @return array
     */
    public function get($onEachSide = 3)
    {
        if ($this->paginator->lastPage() < ($onEachSide * 2) + 6) {
            return $this->getSmallSlider();
        }

        return $this->getUrlSlider($onEachSide);
    }

    /**
     * Create a URL slider links.
     *
     * @param  int  $onEachSide
     * @return array
     */
    protected function getUrlSlider($onEachSide)
    {
        $window = $onEachSide * 1;

        if (!$this->hasPages()) {
            return [
                'first'  => null,
                'slider' => null,
                'last'   => null,
            ];
        }

        // If the current page is very close to the beginning of the page range, we will
        // just render the beginning of the page range, followed by the last 2 of the
        // links in this list, since we will not have room to create a full slider.
        if ($this->currentPage() <= $window) {
            return $this->getSliderTooCloseToBeginning($window);
        }

        // If the current page is close to the ending of the page range we will just get
        // this first couple pages, followed by a larger window of these ending pages
        // since we're too close to the end of the list to create a full on slider.
        elseif ($this->currentPage() > ($this->lastPage() - $window)) {
            return $this->getSliderTooCloseToEnding($window);
        }

        // If we have enough room on both sides of the current page to build a slider we
        // will surround it with both the beginning and ending caps, with this window
        // of pages in the middle providing a Google style sliding paginator setup.
        return $this->getFullSlider(1);
    }

}
