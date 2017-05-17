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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Http\Controllers;

use Antares\Contracts\Foundation\Events\FormResponseContract;
use Antares\Foundation\Processor\Security as Processor;
use Antares\Foundation\Http\Breadcrumb\Breadcrumb;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use URL;

class SecurityController extends AdminController implements FormResponseContract
{

    /**
     * breadcrumb instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * constructing
     * 
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(Breadcrumb $breadcrumb)
    {
        parent::__construct();
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        
    }

    /**
     * Shows security settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Processor $processor)
    {
        $this->breadcrumb->onSecurity();
        $data = $processor->index();
        return view('antares/foundation::settings.security.index', $data);
    }

    /**
     * Handles store request.
     *
     * @param Processor $processor
     * @param Request $request
     */
    public function store(Processor $processor, Request $request)
    {
        return $processor->submit($this, $request);
    }

    /**
     * Handles update request.
     *
     * @param Processor $processor
     * @param Request $request
     */
    public function update(Processor $processor, Request $request)
    {
        return $processor->submit($this, $request);
    }

    /**
     * {@inheritdoc}
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function onSuccess($message)
    {
        return $this->redirectWithMessage(URL::previous(), $message);
    }

    /**
     * {@inheritdoc}
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function onFail($message)
    {
        return $this->redirectWithMessage(URL::previous(), $message, 'error')->withInput();
    }

    /**
     * {@inheritdoc}
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function onValidationFailed(MessageBag $messageBag)
    {
        return $this->redirectWithMessage(URL::previous(), $messageBag->first(), 'error')->withInput();
    }

}
