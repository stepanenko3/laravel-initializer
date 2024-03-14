<?php

namespace Stepanenko3\LaravelInitializer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Carbon;
use Stepanenko3\LaravelInitializer\Run;

use function Termwind\terminal;

abstract class AbstractInitializeCommand extends Command
{
    /**
     * Holds the start time of the command.
     *
     * @var float|null
     */
    protected $startedAt;

    /**
     * Holds the start time of the last processed job, if any.
     *
     * @var float|null
     */
    protected $latestStartedAt;

    /**
     * Holds the status of the last processed job, if any.
     *
     * @var string|null
     */
    protected $latestStatus;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Container $container)
    {
        $config = $container->make('config');
        $env = $config->get('app.env');

        $this->components->info($this->title() . ' started');

        $runner = $container->makeWith(Run::class, ['artisanCommand' => $this]);

        $this->startedAt = microtime(true);

        $this->commands($runner, $env);

        $runTime = number_format((microtime(true) - $this->startedAt) * 1000, 2) . 'ms';

        $this->output->newLine();

        if ($runner->doneWithErrors()) {
            $errorMessages = $runner->errorMessages();

            $this->components->error(
                "<fg=gray>[$runTime]</> " . $this->title() . ' done with '

                    . (count($errorMessages) <= 0 ? 'errors.' : sprintf(
                        '[%s] %s:',
                        count($errorMessages),
                        str('error')->plural(count($errorMessages)),
                    )),
            );

            if (count($errorMessages) > 0) {
                $i = 0;

                foreach ($runner->errorMessages() as $message) {
                    $this->output->write("  <fg=gray>{$i}</> {$message}");
                    $this->output->newLine();

                    $i++;
                }

                $this->output->newLine();
            }

            return Command::FAILURE;
        }

        $this->components->info("<fg=gray>[$runTime]</> " . $this->title() . ' done!');

        return Command::SUCCESS;
    }

    public function writeOutput($text, $status)
    {
        if ($status == 'starting') {
            $this->latestStartedAt = microtime(true);
            $this->latestStatus = $status;

            $formattedStartedAt = Carbon::now()->format('Y-m-d H:i:s');

            return $this->output->write("  <fg=gray>{$formattedStartedAt}</> {$text}");
        }

        if ($this->latestStatus && $this->latestStatus != 'starting') {
            $formattedStartedAt = Carbon::createFromTimestamp($this->latestStartedAt)->format('Y-m-d H:i:s');

            $this->output->write("  <fg=gray>{$formattedStartedAt}</> {$text}");
        }

        $runTime = number_format((microtime(true) - $this->latestStartedAt) * 1000, 2) . 'ms';
        $dots = max(terminal()->width() - mb_strlen($text) - mb_strlen($runTime) - 31, 0);

        $this->output->write(' ' . str_repeat('<fg=gray>.</>', $dots));
        $this->output->write(" <fg=gray>$runTime</>");

        $this->output->writeln(match ($this->latestStatus = $status) {
            'success' => ' <fg=green;options=bold>DONE</>',
            'released_after_exception' => ' <fg=yellow;options=bold>FAIL</>',
            default => ' <fg=red;options=bold>FAIL</>',
        });
    }

    public function choice(
        $question,
        array $choices,
        $default = null,
        #[Deprecated]
        $attempts = null,
        #[Deprecated]
        $multiple = false,
    ) {
        return $this->components->choice($question, $choices, $default);
    }

    abstract protected function title(): string;
}
