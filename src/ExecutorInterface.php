<?php
namespace Graviton\JsonCommand;

use Graviton\JsonCommand\Exception\RuntimeException;

/**
 * Command executor interface
 */
interface ExecutorInterface
{
    /**
     * Execute command with arguments
     *
     * @param array $arguments Command line arguments
     * @return mixed
     * @throws RuntimeException
     */
    public function executeCommand(array $arguments);
}
