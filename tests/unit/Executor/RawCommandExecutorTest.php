<?php
namespace Graviton\JsonCommand\UnitTest\Executor;

use Graviton\JsonCommand\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Graviton\JsonCommand\Executor\RawCommandExecutor;

/**
 * RawCommandExecutor class test
 */
class RawCommandExecutorTest extends TestCase
{
    /**
     * @covers RawCommandExecutor::executeCommand
     */
    public function testExecuteCommandWithErrorExecuting()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(100);

        $arguments = ['a', 'b'];

        /** @var Process|\PHPUnit_Framework_MockObject_MockObject $process */
        $process = $this->getMockBuilder('Symfony\\Component\\Process\\Process')
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();
        $process->expects($this->once())
            ->method('run')
            ->willReturn(1);
        $process->disableOutput();

        /** @var RawCommandExecutor|\PHPUnit_Framework_MockObject_MockObject $commandExecutor */
        $commandExecutor = $this->getMockBuilder('Graviton\\JsonCommand\\Executor\\RawCommandExecutor')
            ->setMethods(['createProcess'])
            ->setConstructorArgs(['php'])
            ->getMock();
        $commandExecutor
            ->expects($this->once())
            ->method('createProcess')
            ->with($arguments)
            ->willReturn($process);

        $commandExecutor->executeCommand($arguments);
    }

    /**
     * @covers RawCommandExecutor::executeCommand
     */
    public function testExecuteCommandSuccess()
    {
        $arguments = ['a', 'b'];
        $rawOutput = '{"abc": "def"}';

        /** @var Process|\PHPUnit_Framework_MockObject_MockObject $process */
        $process = $this->getMockBuilder('Symfony\\Component\\Process\\Process')
            ->disableOriginalConstructor()
            ->setMethods(['run', 'getOutput'])
            ->getMock();
        $process->expects($this->once())
            ->method('run')
            ->willReturn(0);
        $process->expects($this->once())
            ->method('getOutput')
            ->willReturn($rawOutput);
        $process->disableOutput();

        /** @var RawCommandExecutor|\PHPUnit_Framework_MockObject_MockObject $commandExecutor */
        $commandExecutor = $this->getMockBuilder('Graviton\\JsonCommand\\Executor\\RawCommandExecutor')
            ->setMethods(['createProcess', 'serializeArguments'])
            ->setConstructorArgs(['php'])
            ->getMock();
        $commandExecutor
            ->expects($this->once())
            ->method('createProcess')
            ->with($arguments)
            ->willReturn($process);

        $this->assertEquals($rawOutput, $commandExecutor->executeCommand($arguments));
    }
}
