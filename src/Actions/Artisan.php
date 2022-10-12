<?php

namespace Stepanenko3\LaravelInitializer\Actions;

use Illuminate\Console\Command;

class Artisan extends Action
{
    public function __construct(
        Command $artisanCommand,
        private string $command,
        private array $arguments = [],
    ) {
        parent::__construct($artisanCommand);
    }

    public function title(): string
    {
        $aboutCommand = $this->getArtisanCommnad()
            ->getApplication()
            ->find($this->command)
            ->getDescription();

        return "<comment>artisan</comment> $this->command ($aboutCommand)";
    }

    public function run(): bool
    {
        return !$this->getArtisanCommnad()->callSilent($this->command, $this->arguments);
    }
}
