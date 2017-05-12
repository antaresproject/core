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

namespace Antares\Brands\Adapter;

use Antares\Brands\Contracts\StylerAdapter;

class LayoutStyler extends AbstractAdapter implements StylerAdapter
{

    /**
     * creates styles for first text colots
     * 
     * @return String
     */
    protected function brandLeftFirstColors()
    {
        $return = '';
        if (!is_null($value  = array_get($this->colors, 'text.main.first'))) {
            $value  = starts_with($value, '#') ? $value : '#' . $value;
            $return .= "#table-ma { color:" . $value . "}\n";
            $return .= ".menu-aside li a span, .menu-aside li a i{color:" . $value . "}\n";
            $return .= ".breadcrumbs,.breadcrumbs > li:last-child .ddown__init a, .breadcrumbs .ddown .ddown__content .ddown__arrow:after,.breadcrumbs .ddown .ddown__init.ddown__init--white:after, .ddown .ddown__init.ddown__init--white i {color:" . $value . "}\n";
            $return .= ".search-box:before, .search-box > i {color:" . $value . "}\n";
            $return .= ".item-grp  i {color:" . $value . "}\n";
            $return .= ".btn.btn--brand{color:" . $value . "}\n";
            $return .= ".ddown .ddown__init.ddown__init--white:after, .ddown .ddown__init.ddown__init--white i{color:" . $value . "}\n";
        }
        return $return;
    }

    /**
     * creates styles for second text colots
     * 
     * @return String
     */
    protected function brandLeftSecondColors()
    {
        $return = '';
        if (!is_null($value  = array_get($this->colors, 'text.secondary.first'))) {
            $value  = starts_with($value, '#') ? $value : '#' . $value;
            $return .= "aside.main-sidebar ul.main-menu > li > a, aside.main-sidebar ul.main-menu > li > a .icon, aside.main-sidebar ul.main-menu > li > a i{color:" . $value . "}\n";
        }
        if (!is_null($value = array_get($this->colors, 'text.background.first'))) {
            $value  = starts_with($value, '#') ? $value : '#' . $value;
            $return .= ".form-block label:not(.switch) {color:" . $value . "}\n";
            $return .= ".mdl-textfield label:not(.switch) {color:" . $value . "}\n";
        }
        if (!is_null($value = array_get($this->colors, 'text.main.second'))) {
            $value  = starts_with($value, '#') ? $value : '#' . $value;
            $return .= ".search-box .search-box__search-field,.btn.btn--default,.sandbox-mode-title {color:" . $value . "}\n";
            $return .= ".main-head .search-box .search-box__mdl-textfield .mdl-textfield__label {color:" . $value . "}\n";
            $return .= ".search-box .search-box__search-field{ border-color:" . $value . "}\n";
        }
        if (!is_null($value = array_get($this->colors, 'text.secondary.second'))) {
            $value  = starts_with($value, '#') ? $value : '#' . $value;
            $return .= ".main-menu .is-active .icon, .main-menu .is-active i, .main-menu .is-active .text { color:" . $value . " !important;}\n";
            $return .= ".context-menu-list .context-menu-item.context-menu-hover > i, .context-menu-list .context-menu-item:hover > i,.context-menu-list .context-menu-item.context-menu-hover > span, .context-menu-list .context-menu-item:hover > span { color:" . $value . " !important;}\n";
            $return .= "aside.main-sidebar ul.main-menu > li > a:hover, aside.main-sidebar ul.main-menu > li > a:hover .icon, aside.main-sidebar ul.main-menu > li > a:hover i{color:" . $value . " !important}\n";
            $return .= "aside.main-sidebar ul.main-menu > li.hovered .text, aside.main-sidebar ul.main-menu > li.hovered .icon, aside.main-sidebar ul.main-menu > li.hovered i{color:" . $value . "}\n";
        }
        if (!is_null($value = array_get($this->colors, 'text.background.second'))) {
            $value  = starts_with($value, '#') ? $value : '#' . $value;
            $return .= "form fieldset legend{color:" . $value . "}\n";
        }

        return $return;
    }

    /**
     * creates styles for main first containers
     * 
     * @return String
     */
    protected function brandFirstColors()
    {
        $return = '@media only screen and (max-width: 768px){
                        #app-wrapper aside.main-sidebar .main-sidebar__logo{
                            background-image: url(' . brand_logo('big', '/img/theme/antares/logo/logo_mobile.png') . ') !important;
                        }}';

        if (!is_null($value = array_get($this->colors, 'main.value'))) {
            $value  = starts_with($value, '#') ? $value : '#' . $value;
            $return .= ".error-container .top-area,.app-content:before{ background-color:" . $value . " !important}\n";
            $return .= ".ddown.ddown--brand .ddown__menu li.is-selected .flex-block:after{ color:" . $value . " !important;}\n";
            $return .= "aside.main-sidebar ul.main-menu .submenu.submenu--system section.section--2col .submenu__content .submenu__content-right .datarow .datarow__right i{ color:" . $value . " !important;}\n";
            $return .= ".flex-block .flex-block__badge { color:" . $value . " !important;}\n";
            $return .= ".switch .switch-checkbox:checked + .switch-container{ background-color:" . $value . " !important;}\n";
            $return .= ".dropzone-form .dz-message .dz-m__content span { color:" . $value . " !important;}\n";
            $return .= ".ddown .ddown__menu > li:hover > a, .ddown .ddown__menu > li:hover > a i { color:" . $value . " !important;}\n";
            $return .= ".breadcrumbs .ddown__menu { border-color:" . $value . " !important;}\n";
            $return .= "i.zmdi-help-outline.help--trigger:hover { color:" . $value . " !important;}\n";
            $return .= ".ddown.ddown--brand .ddown__menu li.is-selected .flex-block .flex-block__title { color:" . $value . " !important;}\n";
            $return .= ".ddown.ddown--brand .ddown__menu li.is-selected .flex-block { color:" . $value . " !important;}\n";
            $return .= ".timeline li a { color:" . $value . " !important;}\n";
            $return .= ".icheckbox_billevo.checked { background-color: " . $value . "!important;}\n";
            $return .= ".tbl-c table tr td a,.ddown.ddown--columns li.col-is-visible a,.ddown.ddown--columns li.col-is-visible a:after,.btn-link.btn--primary { color: " . $value . "!important;}\n";

            $return .= ".timeline li.timeline__entry--ok:before { color:" . $value . " !important;}\n";
            $return .= ".ddown.ddown--brand .ddown__menu li.is-selected .flex-block, .ddown.ddown--brand .ddown__menu li.is-selected .flex-block .flex-block__title { color:" . $value . " !important;}\n";
            $return .= ".mdl-textfield__label:after { background-color:" . $value . " !important;}\n";
            $return .= ".card.card--chart-small .card__header { background-color:" . $value . " !important;}\n";
            $return .= ".card.card--chart-small .card__header-chart { background-color:" . $value . " !important;}\n";
            $return .= ".card.card--primary-light { background-color:" . $value . " !important;}\n";
            $return .= ".form-block .radio-btns [data-type=radio-btn] input:checked+.btn, .btn.btn--primary { background-color:" . $value . " !important;}\n";
            $return .= ".login-box a { color:" . $value . " !important;}\n";

            $return .= ".ddown-multi .ddown-multi__submenu > li.ddown-multi__return { background-color:" . $value . " !important;}\n";
            $return .= ".dataTables_length a.active { color:" . $value . "}\n";
            $return .= ".card-filter .ddown--filter-edit.ddown--open .card-filter__sgl.ddown__init span:after,.select2-container .select2-selection--multiple .select2-selection__rendered li.select2-selection__choice { background-color:" . $value . "}\n";
            $return .= ".pagination .pagination-filter ul li a:hover,.card-filter .ddown--filter-edit.ddown--open .card-filter__sgl.ddown__init { color:" . $value . "!important;}\n";
            $return .= ".icheck-label:hover > .icheckbox_billevo, .icheckbox_billevo.hover { border-color:" . $value . "!important;}\n";
            $return .= ".tbl-c .dataTables_wrapper .pagination--type2 .dataTables_paginate .paginate_button:hover { color:" . $value . "!important;}\n";

            $return .= "aside.main-sidebar ul.main-menu .submenu.submenu--system section.section--2col .submenu__content .submenu__content-right .datarow .datarow__right i { color:" . $value . "!important;}\n";
            $return .= ".grid-col--menu,.menu-aside-container{background:$value !important;}\n";

            $return .= "@media only screen and (max-width: 768px) {
                    #app-wrapper aside.main-sidebar .mobile-ddowns .mobile-ddowns__sgl ul.mobile-ddowns__menu {
                        background-color: $value
                    }
                }\n";
            $return .= "@media only screen and (max-width: 768px) {
                    aside.main-sidebar .mobile-ddowns .mobile-ddowns__sgl ul.mobile-ddowns__menu li.mobile-ddowns__menu-header {
                        border-bottom: 1px solid $value
                    }
                }\n";


            $return .= ".app-content:before{background:$value !important;}\n";
            $return .= ".app-content__footer .btn--primary{background-color:$value !important;}\n";
            $return .= ".breadcrumbs .ddown__menu{ border-color:$value !important;}\n";
        }
        if (!is_null($modPri1 = array_get($this->colors, 'main.mod1'))) {
            $modPri1 = starts_with($modPri1, '#') ? $modPri1 : '#' . $modPri1;
            $return  .= ".breadcrumbs > li.is-active { background-color:" . $modPri1 . "!important;}\n";
            $return  .= ".menu-aside li.is-active a{background-color:$modPri1 !important;}\n";
        }
        if (!is_null($modPri3 = array_get($this->colors, 'main.mod3'))) {
            $modPri3 = starts_with($modPri3, '#') ? $modPri3 : '#' . $modPri3;
            $return  .= ".badge,.btn.btn--indigo{background-color:$modPri3 !important;}\n";
            $return  .= ".ddown--brand .ddown__init{background-color:$modPri3 !important;}\n";
            $return  .= "body .select2-dropdown ul.select2-results__options li.select2-results__option[aria-selected=true],
                         body .select2-dropdown ul.select2-results__options li.select2-results__option[aria-selected=true]:after,
                         body .select2.select2-container--open .select2-selection__rendered
                         body .select2.select2-container--open .select2-selection__arrow:before,{
                color: $modPri3 !important;
            }";

            $return .= "body .pace .pace-progress {
                background: $modPri3 !important;
            }";
        }
        return $return;
    }

    /**
     * creates styles for second containers
     * 
     * @return String
     */
    protected function brandSecondColors()
    {
        $return = '';
        if (!is_null($value  = array_get($this->colors, 'secondary.value'))) {
            $value  = starts_with($value, '#') ? $value : '#' . $value;
            $return .= "aside.main-sidebar { background-color:" . $value . "}\n";
        }
        if (!is_null($modSec1 = array_get($this->colors, 'secondary.mod1'))) {
            $modSec1 = starts_with($modSec1, '#') ? $modSec1 : '#' . $modSec1;
            $return  .= "aside.main-sidebar ul.main-menu .submenu { background-color:" . $modSec1 . "}\n";
            $return  .= "aside.main-sidebar ul.main-menu > li.more-trigger{border-color:" . $modSec1 . "}\n";
        }
        return $return;
    }

    /**
     * creates style for layout components
     * 
     * @return type
     */
    public function style()
    {

        return '<style data-desc="brand-colors">' .
                $this->brandFirstColors() .
                $this->brandSecondColors() .
                $this->brandLeftFirstColors() .
                $this->brandLeftSecondColors() .
                '</style>';
    }

}
