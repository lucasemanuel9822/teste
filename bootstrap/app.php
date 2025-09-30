<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();
$app->withEloquent();

// Configurar variáveis de ambiente padrão para desenvolvimento local
if (!env('DB_CONNECTION')) {
    putenv('DB_CONNECTION=mysql');
    putenv('DB_HOST=127.0.0.1');
    putenv('DB_PORT=3306');
    putenv('DB_DATABASE=task_management');
    putenv('DB_USERNAME=root');
    putenv('DB_PASSWORD=');
}

// Configurar console
$app->configure('database');

// Configurar Redis
$app->singleton('redis', function () use ($app) {
    return new Illuminate\Redis\RedisManager($app, 'predis', [
        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],
    ]);
});

// Registrar Console Kernel para comandos Artisan
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

// Registrar Service Providers
$app->register(App\Providers\RepositoryServiceProvider::class);
$app->register(MongoDB\Laravel\MongoDBServiceProvider::class);

// Registrar driver MongoDB no DatabaseManager
$app->singleton('db', function ($app) {
    $manager = new Illuminate\Database\DatabaseManager($app, $app['db.factory']);
    
    // Registrar driver MongoDB
    $manager->extend('mongodb', function ($config, $name) {
        return new MongoDB\Laravel\Connection($config);
    });
    
    return $manager;
});

// Registrar controllers no container
$app->bind('App\Http\Controllers\TaskController', function ($app) {
    return new App\Http\Controllers\TaskController(
        $app->make('App\Contracts\Services\TaskServiceInterface')
    );
});

$app->bind('App\Http\Controllers\LogController', function ($app) {
    return new App\Http\Controllers\LogController(
        $app->make('App\Contracts\Services\LogServiceInterface')
    );
});

$app->bind('App\Http\Controllers\LogControllerHybrid', function ($app) {
    try {
        $logService = $app->make('App\Contracts\Services\LogServiceInterface');
        return new App\Http\Controllers\LogControllerHybrid($logService);
    } catch (\Exception $e) {
        // Se não conseguir resolver o LogService, usa sem dependências
        return new App\Http\Controllers\LogControllerHybrid();
    }
});

$app->bind('App\Http\Controllers\SwaggerController', function ($app) {
    return new App\Http\Controllers\SwaggerController();
});

// Configurar Exception Handler usando o método do Lumen
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    function () {
        return new \Laravel\Lumen\Exceptions\Handler();
    }
);

// Registrar middlewares
$app->middleware([
    App\Http\Middleware\SecurityHeadersMiddleware::class
]);

$app->routeMiddleware([
    'auth.api' => App\Http\Middleware\ApiKeyMiddleware::class,
    'throttle' => App\Http\Middleware\ThrottleRequestsMiddleware::class,
]);

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;

