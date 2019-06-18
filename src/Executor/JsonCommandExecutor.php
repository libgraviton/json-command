<?php
namespace Graviton\JsonCommand\Executor;

use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Exception\Exception as SerializerException;
use Graviton\JsonCommand\Exception\RuntimeException;

/**
 * JSON command executor
 *
 * All arguments are serialized to JSON.
 * Command output is deserialized from JSON.
 */
class JsonCommandExecutor extends RawCommandExecutor
{
    /**
     * @var SerializerInterface Serializer
     */
    private $serializer;
    /**
     * @var string Result type passed to Serializer::deserialize()
     */
    private $resultType;

    /**
     * Constructor
     *
     * @param string              $executablePath Executable path
     * @param SerializerInterface $serializer     Serializer
     * @param string              $resultType     Deserialize result type
     */
    public function __construct($executablePath, SerializerInterface $serializer, $resultType)
    {
        $this->serializer = $serializer;
        $this->resultType = $resultType;

        parent::__construct($executablePath);
    }

    /**
     * @inheritdoc
     */
    protected function processArguments(array $arguments)
    {
        return array_map(function ($argument) {
            return $this->serializer->serialize($argument, 'json');
        }, $arguments);
    }

    /**
     * @inheritdoc
     */
    protected function processOutput($output)
    {
        try {
            return $this->serializer->deserialize(trim($output), $this->resultType, 'json');
        } catch (SerializerException $exception) {
            throw new RuntimeException(
                'Cannot deserialize command output',
                RuntimeException::ERROR_DESERIALIZATION,
                $exception
            );
        }
    }
}
