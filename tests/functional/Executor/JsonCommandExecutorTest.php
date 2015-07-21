<?php
namespace Xiag\JsonCommand\FunctionalTest\Executor;

use JMS\Serializer\SerializerBuilder;
use Xiag\JsonCommand\Executor\JsonCommandExecutor;

/**
 * JsonCommandExecutor class test
 */
class JsonCommandExecutorTest extends \PHPUnit_Framework_TestCase
{
    private $execDir;
    private $serializer;

    /**
     * @inheritdoc
     */
    protected function setUp()
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
     *
     * @expectedException \Xiag\JsonCommand\Exception\RuntimeException
     * @expectedExceptionCode 100
     */
    public function testExecuteCommandError()
    {
        $commandExecutor = new JsonCommandExecutor($this->execDir.'/error', $this->serializer, 'array');
        $commandExecutor->executeCommand();
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     *
     * @expectedException \Xiag\JsonCommand\Exception\RuntimeException
     * @expectedExceptionCode 100
     */
    public function testExecuteCommandUnknown()
    {
        $commandExecutor = new JsonCommandExecutor($this->execDir.'/unknown', $this->serializer, 'array');
        $commandExecutor->executeCommand();
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     *
     * @expectedException \Xiag\JsonCommand\Exception\RuntimeException
     * @expectedExceptionCode 100
     */
    public function testExecuteCommandNonExecutable()
    {
        $commandExecutor = new JsonCommandExecutor($this->execDir.'/nonexecutable', $this->serializer, 'array');
        $commandExecutor->executeCommand();
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     *
     * @expectedException \Xiag\JsonCommand\Exception\RuntimeException
     * @expectedExceptionCode 200
     */
    public function testExecuteCommandInvalidJson()
    {
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
