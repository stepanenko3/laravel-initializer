<?php

namespace Stepanenko3\LaravelInitializer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class MakeEchoServerConfig
{
    use Dispatchable, Queueable;

    public function __construct(
        protected array $config = [],
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): string
    {
        $path = base_path('laravel-echo-server.json');

        file_put_contents(
            $path,
            json_encode(array_replace_recursive([
                'authHost' => url('/'),
                'authEndpoint' => '/broadcasting/auth',
                'database' => 'redis',
                'databaseConfig' => [
                    'redis' => [
                        'host' => config('database.redis.default.host'),
                        'port' => config('database.redis.default.port'),
                    ],
                    'sqlite' => [
                        'databasePath' => '/storage/laravel-echo-server.sqlite',
                    ],
                ],
                'devMode' => config('app.debug'),
                'host' => parse_url(url('/'), PHP_URL_HOST),
                'port' => 6001,
                'protocol' => 'http',
                'socketio' => [],
                'sslCertPath' => '',
                'sslKeyPath' => '',
                'sslCertChainPath' => '',
                'sslPassphrase' => '',
                'apiOriginAllow' => [
                    'allowCors' => false,
                    'allowOrigin' => '',
                    'allowMethods' => '',
                    'allowHeaders' => '',
                ],
            ], $this->config), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        return 'Config file for web-socket server created. File: ' . $path;
    }
}
