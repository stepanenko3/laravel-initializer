<?php

namespace Stepanenko3\LaravelInitializer;

use Illuminate\Console\Command;
use Illuminate\Support\Traits\Conditionable;
use Stepanenko3\LaravelInitializer\Contracts\Runner as RunnerContract;
use Stepanenko3\LaravelInitializer\Actions\Action;
use Stepanenko3\LaravelInitializer\Actions\Artisan;
use Stepanenko3\LaravelInitializer\Actions\Callback;
use Stepanenko3\LaravelInitializer\Actions\Dispatch;
use Stepanenko3\LaravelInitializer\Actions\External;
use Stepanenko3\LaravelInitializer\Actions\Publish;
use Stepanenko3\LaravelInitializer\Actions\PublishTag;

class Run implements RunnerContract
{
    use Conditionable;

    private $errorMessages = [];

    private $doneWithErrors = false;

    public function __construct(
        protected Command $artisanCommand,
    ) {
        //
    }

    public function errorMessages(): array
    {
        return $this->errorMessages;
    }

    public function choice(
        string $question,
        array $choices,
    ): self {
        $choice = $this->artisanCommand->choice(
            $question,
            array_keys($choices),
        );

        $callback = $choices[$choice] ?? null;

        if ($callback && is_callable($callback)) {
            return $callback($this, $choice) ?? $this;
        }

        return $this;
    }

    public function doneWithErrors(): bool
    {
        return $this->doneWithErrors;
    }

    public function artisan(string $command, array $arguments = []): RunnerContract
    {
        return $this->run(new Artisan($this->artisanCommand, $command, $arguments));
    }

    public function publish($providers, bool $force = false): RunnerContract
    {
        return $this->run(new Publish($this->artisanCommand, $providers, $force));
    }

    public function publishForce($providers): RunnerContract
    {
        return $this->publish($providers, true);
    }

    public function publishTag($tag, bool $force = false): RunnerContract
    {
        return $this->run(new PublishTag($this->artisanCommand, $tag, $force));
    }

    public function publishTagForce($tag): RunnerContract
    {
        return $this->publishTag($tag, true);
    }

    public function external(string $command, ...$arguments): RunnerContract
    {
        return $this->run(new External($this->artisanCommand, $command, $arguments));
    }

    public function callable(callable $function, ...$arguments): RunnerContract
    {
        return $this->run(new Callback($this->artisanCommand, $function, $arguments));
    }

    public function dispatch($job): RunnerContract
    {
        return $this->run(new Dispatch($this->artisanCommand, $job));
    }

    public function dispatchNow($job): RunnerContract
    {
        return $this->run(new Dispatch($this->artisanCommand, $job, true));
    }

    private function run(Action $action): self
    {
        $action();

        if ($action->failed()) {
            if (!$this->doneWithErrors) {
                $this->doneWithErrors = true;
            }

            if ($message = $action->errorMessage()) {
                $this->errorMessages[] = $message;
            }
        }

        return $this;
    }
}
