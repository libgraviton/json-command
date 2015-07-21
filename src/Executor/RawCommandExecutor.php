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
     * Constructor
     *
     * @param string $executablePath Executable path
     */
    public function __construct($executablePath)
    {
        $this->executablePath = $executablePath;
    }

    /**
     * Add command prefix. Prefix will not serialized
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
            ->setPrefix($this->getCommandPrefix())
            ->getProcess();
    }
}
