<?php
namespace Graviton\JsonCommand\FunctionalTest\Executor;

use Graviton\JsonCommand\Exception\RuntimeException;
use Graviton\JsonCommand\Executor\RawCommandExecutor;
use PHPUnit\Framework\TestCase;

/**
 * RawCommandExecutor class test
 */
class RawCommandExecutorTest extends TestCase
{
    private $execDir;

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
     */
    public function testExecuteCommandError()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(100);
        $commandExecutor = new RawCommandExecutor($this->execDir.'/error');
        $commandExecutor->executeCommand();
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     */
    public function testExecuteCommandUnknown()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(100);
        $commandExecutor = new RawCommandExecutor($this->execDir.'/unknown');
        $commandExecutor->executeCommand();
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
     */
    public function testExecuteCommandNonExecutable()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(100);
        $commandExecutor = new RawCommandExecutor($this->execDir.'/nonexecutable');
        $commandExecutor->executeCommand();
    }
}
