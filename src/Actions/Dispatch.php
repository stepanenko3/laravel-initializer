<?php

namespace Stepanenko3\LaravelInitializer\Actions;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;

class Dispatch extends Action
{
    public function __construct(
        Command $artisanCommand,
        private $job,
        private bool $runNow = false,
    ) {
        parent::__construct($artisanCommand);
    }

    public function title(): string
    {
        return '<comment>dispatch</comment> ' . get_class($this->job);
    }

    public function run(): bool
    {
        $result = $this->runNow
            ? Container::getInstance()->make(Dispatcher::class)->dispatchNow($this->job)
            : Container::getInstance()->make(Dispatcher::class)->dispatch($this->job);

        return !(is_int($result) && $result > 0);
    }
}
