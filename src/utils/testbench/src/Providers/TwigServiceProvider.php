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


namespace Antares\Testbench\Providers;

use Twig_Loader_Chain;
use Twig_Loader_Array;
use TwigBridge\ServiceProvider;
use TwigBridge\Engine\Compiler;
use TwigBridge\Engine\Twig;
use TwigBridge\Bridge;
use Illuminate\View\ViewServiceProvider;
use InvalidArgumentException;
use TwigBridge\Twig\Loader;

class TwigServiceProvider extends ServiceProvider
{

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerCommands();
        $this->registerOptions();
        $this->registerLoaders();
        $this->registerEngine();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadConfiguration();
        $this->registerExtension();
    }

    /**
     * Check if we are running Lumen or not.
     * 
     * @return bool
     */
    protected function isLumen()
    {
        return strpos($this->app->version(), 'Lumen') !== false;
    }

    /**
     * Load the configuration files and allow them to be published.
     * 
     * @return void
     */
    protected function loadConfiguration()
    {
        $configPath = __DIR__ . '/../../fixture/config/twigbridge.php';
        if (!$this->isLumen()) {
            $this->publishes([$configPath => config_path('twigbridge.php')], 'config');
        }
        $this->mergeConfigFrom($configPath, 'twigbridge');
    }

    /**
     * Register the Twig extension in the Laravel View component.
     * 
     * @return void
     */
    protected function registerExtension()
    {
        $this->app->make('view')->addExtension($this->app->make('twig.extension'), 'twig', function () {
            return $this->app->make('twig.engine');
        });
    }

    /**
     * Register Twig config option bindings.
     *
     * @return void
     */
    protected function registerOptions()
    {
        $this->app->bindIf('twig.extension', function () {
            return $this->app->make('config')->get('twigbridge.twig.extension');
        });

        $this->app->bindIf('twig.options', function () {
            $options = $this->app->make('config')->get('twigbridge.twig.environment', []);

            // Check whether we have the cache path set
            if (empty($options['cache'])) {
                // No cache path set for Twig, lets set to the Laravel views storage folder
                $options['cache'] = storage_path('framework/views/twig');
            }

            return $options;
        });

        $this->app->bindIf('twig.extensions', function () {
            $load = $this->app->make('config')->get('twigbridge.extensions.enabled', []);

            // Is debug enabled?
            // If so enable debug extension
            $options = $this->app->make('twig.options');
            $isDebug = (bool) (isset($options['debug'])) ? $options['debug'] : false;

            if ($isDebug) {
                array_unshift($load, 'Twig_Extension_Debug');
            }

            return $load;
        });

        $this->app->bindIf('twig.lexer', function () {
            return null;
        });
    }

    /**
     * Register Twig loader bindings.
     *
     * @return void
     */
    protected function registerLoaders()
    {
        // The array used in the ArrayLoader
        $this->app->bindIf('twig.templates', function () {
            return [];
        });

        $this->app->bindIf('twig.loader.array', function ($app) {
            return new Twig_Loader_Array($app->make('twig.templates'));
        });

        $this->app->bindIf('twig.loader.viewfinder', function () {
            return new Loader($this->app->make('files'), $this->app->make('view')->getFinder(), $this->app->make('twig.extension'));
        });

        $this->app->bindIf('twig.loader', function () {
            return new Twig_Loader_Chain([
                $this->app->make('twig.loader.array'),
                $this->app->make('twig.loader.viewfinder'),
            ]);
        }, true
        );
    }

    /**
     * Register Twig engine bindings.
     *
     * @return void
     */
    protected function registerEngine()
    {
        $this->app->bindIf('twig', function () {
            $extensions = $this->app->make('twig.extensions');
            $lexer      = $this->app->make('twig.lexer');
            $twig       = new Bridge(
                    $this->app->make('twig.loader'), $this->app->make('twig.options'), $this->app
            );
            // Instantiate and add extensions
            foreach ($extensions as $extension) {
                // Get an instance of the extension
                // Support for string, closure and an object
                if (is_string($extension)) {
                    try {
                        $extension = $this->app->make($extension);
                    } catch (\Exception $e) {

//                        throw new InvalidArgumentException(
//                        "Cannot instantiate Twig extension '$extension': " . $e->getMessage()
//                        );
                    }
                } elseif (is_callable($extension)) {
                    $extension = $extension($this->app, $twig);
                } elseif (!is_a($extension, 'Twig_Extension')) {
                    throw new InvalidArgumentException('Incorrect extension type');
                }
                if ($extension instanceof \Twig_ExtensionInterface) {
                    $twig->addExtension($extension);
                }
            }

            // Set lexer
            if (is_a($lexer, 'Twig_LexerInterface')) {
                $twig->setLexer($lexer);
            }

            return $twig;
        }, true
        );

        $this->app->alias('twig', 'Twig_Environment');
        $this->app->alias('twig', 'TwigBridge\Bridge');

        $this->app->bindIf('twig.compiler', function () {
            return new Compiler($this->app->make('twig'));
        });

        $this->app->bindIf('twig.engine', function () {
            return new Twig(
                    $this->app->make('twig.compiler'), $this->app->make('twig.loader.viewfinder'), $this->app->make('config')->get('twigbridge.twig.globals', [])
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.twig',
            'command.twig.clean',
            'command.twig.lint',
            'twig.extension',
            'twig.options',
            'twig.extensions',
            'twig.lexer',
            'twig.templates',
            'twig.loader.array',
            'twig.loader.viewfinder',
            'twig.loader',
            'twig',
            'twig.compiler',
            'twig.engine',
        ];
    }

}
