<?php
namespace Graviton\JsonCommand\Exception;

/**
 * Runtime exception
 */
class RuntimeException extends \RuntimeException
{
    const ERROR_PROCESS_FAILED = 100;
    const ERROR_DESERIALIZATION = 200;
}
