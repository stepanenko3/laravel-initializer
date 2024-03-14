<?php

namespace Stepanenko3\LaravelInitializer\Contracts;

interface Runner
{
    public function when($value = null, ?callable $callback = null, ?callable $default = null);

    public function unless($value = null, ?callable $callback = null, ?callable $default = null);

    public function choice(string $question, array $choices): self;

    public function errorMessages(): array;

    public function doneWithErrors(): bool;

    public function artisan(string $command, array $arguments = []): self;

    public function external(string $command, ...$arguments): self;

    public function callable(callable $function, ...$arguments): self;

    public function dispatch($job): self;

    public function dispatchNow($job): self;

    public function publish($providers, bool $force = false): self;

    public function publishForce($providers): self;

    public function publishTag($tag, bool $force = false): self;

    public function publishTagForce($tag): self;
}
