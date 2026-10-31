<?php

declare(strict_types=1);

namespace App;

use Zana\Cache\CacheServiceProvider;
use Zana\Console\ConsoleServiceProvider;
use Zana\Core\Application;
use Zana\Core\ApplicationBuilder;
use Zana\Database\DatabaseServiceProvider;
use Zana\Http\HttpServiceProvider;
use Zana\Logging\LoggingServiceProvider;
use Zana\Maker\MakerServiceProvider;
use Zana\Mediator\MediatorServiceProvider;
use Zana\Routing\RoutingServiceProvider;
use Zana\Security\SecurityServiceProvider;
use Zana\Serialization\SerializationServiceProvider;
use Zana\Validation\ValidationServiceProvider;

/**
 * The application: the framework's providers here, the features in the
 * modules listed in config/modules.php. Logging comes first, so errors are
 * written to var/log/app.log. The make: commands are there in development,
 * where mj-zana/maker is installed.
 */
final class Bootstrap
{
    public static function builder(string $basePath): ApplicationBuilder
    {
        return Application::configure($basePath)
            ->withProviders(
                LoggingServiceProvider::class,
                HttpServiceProvider::class,
                RoutingServiceProvider::class,
                CacheServiceProvider::class,
                SecurityServiceProvider::class,
                MediatorServiceProvider::class,
                SerializationServiceProvider::class,
                ValidationServiceProvider::class,
                DatabaseServiceProvider::class,
                ConsoleServiceProvider::class,
                ...(class_exists(MakerServiceProvider::class) ? [MakerServiceProvider::class] : []),
            );
    }
}
