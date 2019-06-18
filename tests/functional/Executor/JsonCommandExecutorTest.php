<?php
namespace Graviton\JsonCommand\FunctionalTest\Executor;

use Graviton\JsonCommand\Exception\RuntimeException;
use JMS\Serializer\SerializerBuilder;
use Graviton\JsonCommand\Executor\JsonCommandExecutor;
use PHPUnit\Framework\TestCase;

/**
 * JsonCommandExecutor class test
 */
class JsonCommandExecutorTest extends TestCase
{
    private $execDir;
    private $serializer;

    /**
     * @inheritdoc
     */
    protected function setUp() : void
    {
        $this->execDir = realpath(__DIR__ . '/../Resources/commands');
        chmod($this->execDir.'/success', 0755);
        chmod($this->execDir.'/error', 0755);
        chmod($this->execDir.'/invalidjson', 0755);
        chmod($this->execDir.'/nonexecutable', 0600);

        $this->serializer = SerializerBuilder::create()
            ->addDefaultHandlers()
            ->addDefaultSerializationVisitors()
            ->addDefaultDeserializationVisitors()
            ->setDebug(true)
            ->build();


        parent::setUp();
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     */
    public function testExecuteCommandEcho()
    {
        $commandExecutor = new JsonCommandExecutor('echo', $this->serializer, 'string');
        $this->assertEquals('123', $commandExecutor->executeCommand(['123']));
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     */
    public function testExecuteCommandCustom()
    {
        $commandExecutor = new JsonCommandExecutor($this->execDir.'/success', $this->serializer, 'array');
        $this->assertEquals(
            [
                ['res' => 'abc'],
                ['res' => 123],
            ],
            $commandExecutor->executeCommand(['abc', 123])
        );
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     */
    public function testExecuteCommandError()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(100);
        $commandExecutor = new JsonCommandExecutor($this->execDir.'/error', $this->serializer, 'array');
        $commandExecutor->executeCommand();
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     */
    public function testExecuteCommandUnknown()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(100);
        $commandExecutor = new JsonCommandExecutor($this->execDir.'/unknown', $this->serializer, 'array');
        $commandExecutor->executeCommand();
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     */
    public function testExecuteCommandNonExecutable()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(100);
        $commandExecutor = new JsonCommandExecutor($this->execDir.'/nonexecutable', $this->serializer, 'array');
        $commandExecutor->executeCommand();
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     */
    public function testExecuteCommandInvalidJson()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(200);

        $serializer = SerializerBuilder::create()
            ->addDefaultHandlers()
            ->addDefaultSerializationVisitors()
            ->addDefaultDeserializationVisitors()
            ->setDebug(true)
            ->build();

        $commandExecutor = new JsonCommandExecutor($this->execDir.'/invalidjson', $serializer, 'array');
        $commandExecutor->executeCommand();
    }
}
