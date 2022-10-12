<?php

namespace Stepanenko3\LaravelInitializer\Jobs\Supervisor;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;

abstract class MakeSupervisorConfig
{
    use Dispatchable, Queueable;

    /**
     * Name of the supervisor process.
     */
    protected string $processName = '';

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $params = [],
        protected string $fileName = '',
        protected string $path = '/etc/supervisor/conf.d/'
    ) {
        if (!$this->fileName) {
            $this->fileName = $this->configName() . '.conf';
        }
    }

    /**
     * Execute the job.
     *
     * @return string
     */
    public function handle(): string
    {
        file_put_contents(
            $this->path . $this->fileName,
            $this->makeSupervisorConfig($this->processName, $this->params)
        );

        return 'Supervisor config file created. Path: ' . $this->path . $this->fileName;
    }

    protected function makeSupervisorConfig(string $programName, array $data): string
    {
        $default_config = [
            'process_name' => '%(program_name)s_%(process_num)02d',
            'directory' => base_path(),
            'autostart' => true,
            'autorestart' => true,
            'user' => get_current_user(),
            'numprocs' => 1,
            'redirect_stderr' => true,
            'stdout_logfile' => "{$this->getLogsPath()}/{$this->configName()}.log",
        ];
        $data = array_merge($default_config, $data);

        $app_name = Str::slug($this->getApplicationName());
        $config = "[program:$app_name-$programName]" . PHP_EOL;

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } else {
                $value = (string) $value;
            }

            $config .= "$key=$value" . PHP_EOL;
        }

        return $config;
    }

    protected function getLogsPath(): string
    {
        return storage_path('logs');
    }

    /**
     * @return string
     */
    protected function configName(): string
    {
        return Str::slug($this->getApplicationName() . '-' . $this->processName);
    }

    protected function getApplicationName(): string
    {
        return config('app.name');
    }
}
