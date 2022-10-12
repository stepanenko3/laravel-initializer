<?php

namespace Stepanenko3\LaravelInitializer\Jobs\Supervisor;

class MakeSocketSupervisorConfig extends MakeSupervisorConfig
{
    protected string $processName = 'socket';

    public function __construct(
        array $params = [],
        string $fileName = '',
        string $path = '/etc/supervisor/conf.d/',
    ) {
        $params = $params ?: [
            'command' => 'node ./node_modules/.bin/laravel-echo-server start',
        ];

        parent::__construct($params, $fileName, $path);
    }
}
