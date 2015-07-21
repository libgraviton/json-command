<?php
namespace Xiag\JsonCommand\FunctionalTest\Executor;

use Xiag\JsonCommand\Executor\RawCommandExecutor;

/**
 * RawCommandExecutor class test
 */
class RawCommandExecutorTest extends \PHPUnit_Framework_TestCase
{
    private $execDir;

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

        parent::setUp();
    }

    /**
     * @covers RawCommandExecutor::executeCommand
     */
    public function testExecuteCommandEcho()
    {
        $commandExecutor = new RawCommandExecutor('echo');
        $this->assertEquals('123', $commandExecutor->executeCommand(['123']));
    }

    /**
     * @covers RawCommandExecutor::executeCommand
     */
    public function testExecuteCommandCustom()
    {
        $commandExecutor = new RawCommandExecutor($this->execDir.'/success');
        $this->assertEquals(
            '[{"res":"abc"},{"res":123}]',
            $commandExecutor->executeCommand(['"abc"', '123'])
        );
    }

    /**
     * @covers RawCommandExecutor::executeCommand
     *
     * @expectedException \Xiag\JsonCommand\Exception\RuntimeException
     * @expectedExceptionCode 100
     */
    public function testExecuteCommandError()
    {
        $commandExecutor = new RawCommandExecutor($this->execDir.'/error');
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
        $commandExecutor = new RawCommandExecutor($this->execDir.'/unknown');
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
        $commandExecutor = new RawCommandExecutor($this->execDir.'/nonexecutable');
        $commandExecutor->executeCommand();
    }
}
