<?php

namespace Stepanenko3\LaravelInitializer\Actions;

use Exception;
use Illuminate\Console\Command;

abstract class Action
{
    private bool $failed = false;

    protected string | null $errorMessage = null;

    public function __construct(
        private Command $artisanCommnad,
    ) {
    }

    public function __invoke(): bool
    {
        $this->getArtisanCommnad()
            ->writeOutput($this->title(), 'starting');

        $failed = !(function () {
            try {
                return $this->run();
            } catch (Exception $e) {
                $this->errorMessage = get_class($e) . ': ' . $e->getMessage();

                return false;
            }
        })();

        $this->failed = $failed;

        $this->getArtisanCommnad()
            ->writeOutput($this->title(), $failed ? 'failed' : 'success');

        return !$failed;
    }

    public function failed(): bool
    {
        return $this->failed;
    }

    public function errorMessage(): ?string
    {
        return $this->errorMessage;
    }

    abstract public function title(): string;

    abstract public function run(): bool;

    public function getArtisanCommnad(): Command
    {
        return $this->artisanCommnad;
    }
}
