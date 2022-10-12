<?php

namespace Stepanenko3\LaravelInitializer\Actions;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\Process;

class External extends Action
{
    public function __construct(
        Command $artisanCommand,
        private string $command,
        private array $arguments = []
    ) {
        parent::__construct($artisanCommand);
    }

    public function title(): string
    {
        $argString = implode(' ', $this->arguments);

        return "<comment>bash</comment> $this->command $argString";
    }

    public function run(): bool
    {
        $Process = $this->createProcess();
        $Process->setTimeout(null);
        $Process->run();

        $error = $Process->getErrorOutput();
        $exitCode = $Process->getExitCode();

        if ($error and $exitCode > 0) {
            throw new RuntimeException(trim($error));
        }

        return !$exitCode;
    }

    private function createProcess(): Process
    {
        if (empty($this->arguments)) {
            return Process::fromShellCommandline($this->command);
        }

        return new Process(array_merge([$this->command], $this->arguments));
    }
}
