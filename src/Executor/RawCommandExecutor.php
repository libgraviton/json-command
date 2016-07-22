<?php
namespace Xiag\JsonCommand\Executor;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Xiag\JsonCommand\ExecutorInterface;
use Xiag\JsonCommand\Exception\RuntimeException;

/**
 * Command executor
 */
class RawCommandExecutor implements ExecutorInterface
{
    /**
     * @var string Executable path
     */
    private $executablePath;
    /**
     * @var array Prefix arguments
     */
    private $prefixArguments = [];
    /**
     * @var array Environment variables
     */
    private $environmentVariables = [];

    /**
     * Constructor
     *
     * @param string $executablePath Executable path
     */
    public function __construct($executablePath)
    {
        $this->executablePath = $executablePath;
    }

    /**
     * Add command prefix. Prefix will not be serialized
     *
     * @param string $argument
     * @return $this
     */
    public function addCommandPrefix($argument)
    {
        $this->prefixArguments[] = $argument;
        return $this;
    }

    /**
     * Add environment variable. Value will not be serialized
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addEnvironmentVariable($name, $value)
    {
        $this->environmentVariables[$name] = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function executeCommand(array $arguments = [])
    {
        $process = $this->createProcess($arguments);

        try {
            return $this->processOutput($process->mustRun()->getOutput());
        } catch (ProcessFailedException $exception) {
            throw new RuntimeException(
                sprintf(
                    'Error while executing command: "%s" (code %d)',
                    $process->getExitCodeText(),
                    $process->getExitCode()
                ),
                RuntimeException::ERROR_PROCESS_FAILED,
                $exception
            );
        }
    }

    /**
     * Get command prefix
     *
     * @return array
     */
    protected function getCommandPrefix()
    {
        return array_merge([$this->executablePath], $this->prefixArguments);
    }

    /**
     * Process command output
     *
     * @param string $output Raw command output
     * @return mixed
     */
    protected function processOutput($output)
    {
        return rtrim($output, PHP_EOL);
    }

    /**
     * Serialize command arguments
     *
     * @param array $arguments
     * @return array
     */
    protected function processArguments(array $arguments)
    {
        return $arguments;
    }

    /**
     * @param array $arguments Arguments
     * @return Process
     */
    protected function createProcess(array $arguments)
    {
        return ProcessBuilder::create($this->processArguments($arguments))
            ->addEnvironmentVariables($this->environmentVariables)
            ->setPrefix($this->getCommandPrefix())
            ->getProcess();
    }
}
