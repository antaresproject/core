<?php

declare(strict_types=1);

namespace Antares\Extension;

use Antares\Extension\Console\AclCommand;
use Antares\Extension\Console\ActiveCommand;
use Antares\Extension\Console\DeactiveCommand;
use Antares\Extension\Console\InstallCommand;
use Antares\Extension\Console\ListCommand;
use Antares\Extension\Console\UninstallCommand;
use Antares\Support\Providers\CommandServiceProvider as ServiceProvider;
use Illuminate\Contracts\Container\Container;

class CommandServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
	protected $commands = [
		'Install'             	=> 'antares.commands.extension.install',
		'Uninstall'             => 'antares.commands.extension.uninstall',
        'Active'                => 'antares.commands.extension.active',
        'Deactive'              => 'antares.commands.extension.deactive',
		'List'           		=> 'antares.commands.extension.list',
        'Acl'                   => 'antares.commands.extension.acl',
	];

	protected function registerInstallCommand()
	{
		$this->app->singleton('antares.commands.extension.install', function (Container $app) {
			return $app->make(InstallCommand::class);
		});
	}

	protected function registerUninstallCommand()
	{
		$this->app->singleton('antares.commands.extension.uninstall', function (Container $app) {
			return $app->make(UninstallCommand::class);
		});
	}

    protected function registerActiveCommand()
    {
        $this->app->singleton('antares.commands.extension.active', function (Container $app) {
            return $app->make(ActiveCommand::class);
        });
    }

    protected function registerDeactiveCommand()
    {
        $this->app->singleton('antares.commands.extension.deactive', function (Container $app) {
            return $app->make(DeactiveCommand::class);
        });
    }

	protected function registerListCommand()
	{
		$this->app->singleton('antares.commands.extension.list', function (Container $app) {
			return $app->make(ListCommand::class);
		});
	}

    protected function registerAclCommand()
    {
        $this->app->singleton('antares.commands.extension.acl', function (Container $app) {
            return $app->make(AclCommand::class);
        });
    }

}
