<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Laravel\Console;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Console\Command as LaravelCommand;
use Illuminate\Support\Facades\App;
use Simtabi\Laranail\Nails\Laravel\Console\Events\CommandStarting;
use Simtabi\Laranail\Nails\Laravel\Console\Events\CommandTerminating;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * https://github.com/bmitch/consoleEvents
 */
class Command extends LaravelCommand
{

    /**
     * The Event Dispatcher.
     *
     * @var Dispatcher
     */
    protected $events;

    /**
     * Holds the time it took to execute the command.
     * @var float
     */
    protected $executionTime;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->events = App::make(Dispatcher::class);
    }

    /**
     * Runs the command.
     * @param  InputInterface  $input  Input Interface.
     * @param  OutputInterface $output Output Interface.
     * @return integer
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->events->fire(
            new CommandStarting($this, $input)
        );

        $this->startTimer();
        $exitCode = parent::run($input, $output);
        $this->endTimer();

        $this->events->fire(
            new CommandTerminating($this, $input, $exitCode)
        );

        return $exitCode;
    }

    /**
     * Sets the start time of the command.
     * @return void
     */
    protected function startTimer()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Sets the end time of the command.
     * @return void
     */
    protected function endTimer()
    {
        $this->executionTime = microtime(true) - $this->startTime;
    }

    /**
     * Get the execution time of the command.
     * @return float
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }
}
