<?php

namespace Stepanenko3\LaravelInitializer\Actions;

use Illuminate\Console\Command;

class Callback extends Action
{
    public function __construct(
        Command $artisanCommand,
        private $function,
        private array $arguments = [],
    ) {
        parent::__construct($artisanCommand);
    }

    public function title(): string
    {
        is_callable($this->function, false, $name);

        return '<comment>function</comment> ' . $name;
    }

    public function run(): bool
    {
        $result = call_user_func($this->function, ...$this->arguments);

        return is_bool($result) ? $result : true;
    }
}
