<?php

namespace Stepanenko3\LaravelInitializer\Actions;

use Illuminate\Console\Command;
use InvalidArgumentException;

class PublishTag extends Action
{
    private const COMMAND = 'vendor:publish';

    private array $arguments = [];

    private array $currentArgument = [];

    public function __construct(
        Command $artisanCommand,
        private string | array $tags,
        private bool $force = false
    ) {
        parent::__construct($artisanCommand);
    }

    public function __invoke(): bool
    {
        if (is_string($this->tags)) {
            $this->addTag($this->tags);
        } elseif (is_array($this->tags)) {
            $this->handleArray();
        } else {
            throw new InvalidArgumentException('Invalid publishable argument.');
        }

        foreach ($this->arguments as $argument) {
            $this->currentArgument = $argument;

            $errors = [];
            parent::__invoke();

            if ($this->errorMessage) {
                $errors[] = $this->errorMessage;
            }
        }

        $this->errorMessage = implode(PHP_EOL, $errors);

        return true;
    }

    public function title(): string
    {
        $title = '<comment>publish resource</comment> ';

        $tagStringCallback = function (string $tag) {
            return " Tag [$tag]";
        };

        if (isset($this->currentArgument['--tag'])) {
            if (is_string($this->currentArgument['--tag'])) {
                $title .= $tagStringCallback($this->currentArgument['--tag']);
            } else {
                foreach ($this->currentArgument['--tag'] as $tag) {
                    $title .= $tagStringCallback($tag);
                }
            }
        }

        return trim($title);
    }

    public function run(): bool
    {
        $action = new Artisan($this->getArtisanCommnad(), self::COMMAND, $this->currentArgument);

        return $action->run();
    }

    private function addTag(string $tag): void
    {
        $arguments['--tag'] = $tag;

        if ($this->force) {
            $arguments['--force'] = true;
        }

        $this->arguments[] = $arguments;
    }

    private function handleArray(): void
    {
        foreach ($this->tags as $tag) {
            $this->addTag($tag);
        }
    }
}
