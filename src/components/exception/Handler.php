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

namespace Antares\Exception;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Exception;

class Handler extends ExceptionHandler
{

    /**
     * @var integer maximum number of source code lines to be displayed. Defaults to 25.
     */
    public $maxSourceLines = 25;

    /**
     * @var integer maximum number of trace source code lines to be displayed. Defaults to 10.
     * @since 1.1.6
     */
    public $maxTraceSourceLines = 10;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  \Exception  $e
     *
     * @return Response
     */
    public function render($request, Exception $e)
    {
        try {
            $installed = app('antares.installed');

            $ajax = app('request')->ajax();
            if ($ajax) {
                $this->maxSourceLines      = 1;
                $this->maxTraceSourceLines = 1;
            }
            $factory = app('Illuminate\Contracts\View\Factory');
            $factory->getFinder()->addNamespace('antares/foundation', __DIR__ . '/../foundation/resources/views/');
            if ($e instanceof NotFoundHttpException) {
                return response($factory->make('antares/foundation::exception.404'), 404);
            }
            if ($e instanceof MethodNotAllowedHttpException && !request()->ajax()) {
                return redirect_with_message(url()->previous(), trans('Method not allowed'), 'error');
            }


            if ($e instanceof HttpException && $e->getStatusCode() === 503) {
                return response($factory->make('antares/foundation::exception.maintenance'), 404);
            }

            $fileName  = $e->getFile();
            $errorLine = $e->getLine();
            $trace     = array_slice($e->getTrace(), 0, 21);

            foreach ($trace as $i => $t) {
                if (!isset($t['file']))
                    $trace[$i]['file'] = 'unknown';

                if (!isset($t['line']))
                    $trace[$i]['line'] = 0;

                if (!isset($t['function']))
                    $trace[$i]['function'] = 'unknown';

                unset($trace[$i]['object']);
            }


            $data = [
                'code'      => 500,
                'type'      => get_class($e),
                'errorCode' => $e->getCode(),
                'message'   => $e->getMessage(),
                'file'      => $fileName,
                'line'      => $errorLine,
                'trace'     => $e->getTraceAsString(),
                'traces'    => $trace,
                'version'   => '1.0',
                'time'      => time()
            ];

            $renderSourceCode = function($fileName, $errorLine, $maxLines) {
                return $this->renderSourceCode($fileName, $errorLine, $maxLines);
            };
            $argumentsToString = function($args) {
                return $this->argumentsToString($args);
            };
            $isCoreCode = function($args) {
                return $this->isCoreCode($args);
            };
            $hasCode = function($file) {
                return $file !== 'unknown' && is_file($file);
            };
            $debug     = ($installed) ? memory('site.mode') !== 'production' : env('APP_DEBUG');
            $autoSend  = $installed && memory('notification.send.always') != null;
            $autoSend  = ($ajax or $debug) ? false : $autoSend;
            $arguments = [
                'maxSourceLines'      => $this->maxSourceLines,
                'maxTraceSourceLines' => $this->maxTraceSourceLines,
                'data'                => $data,
                'isCoreCode'          => $isCoreCode,
                'renderSourceCode'    => $renderSourceCode,
                'argumentsToString'   => $argumentsToString,
                'hasCode'             => $hasCode,
                'autoSend'            => $autoSend,
                'installed'           => $installed,
                'url'                 => URL::current(),
                'description'         => 'Auto Exception Notification',
                'ajax'                => $ajax
            ];

            if ($e instanceof ModelNotFoundException) {
                view()->share('content_class', 'error-page');
                return response(view('antares/foundation::exception.404_model_not_found', $arguments), 404);
            }
            if ($e instanceof NotFoundHttpException) {
                return response(view('antares/foundation::exception.404', $arguments), 404);
            }

            $this->scripts($autoSend);
            $viewPath = $debug ? 'antares/foundation::exception.500_details' : 'antares/foundation::exception.500_production';
            array_set($arguments, 'solution', config('solution.' . snake_case(get_class($e))));
            if (!$factory->exists($viewPath)) {
                throw $e;
            }
            $view = null;
            try {
                $view = $factory->make('antares/foundation::exception.500_details', $arguments);
            } catch (Exception $ex) {
                
            }

            if (is_null($view)) {
                throw $e;
            }
            if ($ajax) {
                return response($view, 500);
            }

            $arguments['content'] = $view->render();
            return response($factory->make($viewPath, $arguments), 500);
        } catch (Exception $e) {
            vdump($e);
            exit;
        }
    }

    /**
     * Returns a value indicating whether the call stack is from application code.
     * @param array $trace the trace data
     * @return boolean whether the call stack is from application code.
     */
    protected function isCoreCode($trace)
    {
        if (isset($trace['file'])) {
            $systemPath = realpath(dirname(__FILE__) . '/..');
            return $trace['file'] === 'unknown' || strpos(realpath($trace['file']), $systemPath . DIRECTORY_SEPARATOR) === 0;
        }
        return false;
    }

    /**
     * renders source code of file
     * 
     * @param String $fileName
     * @param numeric $errorLine
     * @param numeric $maxLines
     * @return string
     */
    protected function renderSourceCode($fileName, $errorLine, $maxLines)
    {
        $errorLine--;
        if ($errorLine < 0 || ($lines     = @file($fileName)) === false || ($lineCount = count($lines)) <= $errorLine) {
            return '';
        }
        $halfLines       = (int) ($maxLines / 2);
        $beginLine       = $errorLine - $halfLines > 0 ? $errorLine - $halfLines : 0;
        $endLine         = $errorLine + $halfLines < $lineCount ? $errorLine + $halfLines : $lineCount - 1;
        $lineNumberWidth = strlen($endLine + 1);
        $output          = '';
        for ($i = $beginLine; $i <= $endLine; ++$i) {
            $isErrorLine = $i === $errorLine;
            $code        = sprintf("<span class=\"ln" . ($isErrorLine ? ' error-ln' : '') . "\">%0{$lineNumberWidth}d</span> %s", $i + 1, e(str_replace("\t", '    ', $lines[$i])));
            $output      .= (!$isErrorLine) ? $code : '<span class="error">' . $code . '</span>';
        }
        return '<div class="code"><pre>' . $output . '</pre></div>';
    }

    /**
     * arguments to String decorator
     * @param array $args
     * @return String
     */
    protected function argumentsToString($args)
    {
        $count = 0;

        $isAssoc = $args !== array_values($args);

        foreach ($args as $key => $value) {
            $count++;
            if ($count >= 5) {
                if ($count > 5) {
                    unset($args[$key]);
                } else {
                    $args[$key] = '...';
                }
                continue;
            }

            if (is_object($value)) {
                $args[$key] = get_class($value);
            } elseif (is_bool($value)) {
                $args[$key] = $value ? 'true' : 'false';
            } elseif (is_string($value)) {
                if (strlen($value) > 64) {
                    $args[$key] = '"' . substr($value, 0, 64) . '..."';
                } else {
                    $args[$key] = '"' . $value . '"';
                }
            } elseif (is_array($value)) {
                $args[$key] = 'array(' . $this->argumentsToString($value) . ')';
            } elseif ($value === null) {
                $args[$key] = 'null';
            } elseif (is_resource($value)) {
                $args[$key] = 'resource';
            }

            if (is_string($key)) {
                $args[$key] = '"' . $key . '" => ' . $args[$key];
            } elseif ($isAssoc) {
                $args[$key] = $key . ' => ' . $args[$key];
            }
        }
        $out = implode(", ", $args);

        return $out;
    }

    /**
     * exception scripts
     */
    protected function scripts($autoSend = false)
    {
        if (!app()->bound('antares.asset')) {
            return false;
        }
        publish(null, ['/packages/core/js/exception.js']);

        if ($autoSend) {
            publish(null, ['/packages/core/js/auto-send.js']);
        }
    }

}
