<?php
namespace Xiag\JsonCommand\UnitTest\Executor;

use Symfony\Component\Process\Process;
use Xiag\JsonCommand\Executor\RawCommandExecutor;

/**
 * RawCommandExecutor class test
 */
class RawCommandExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers RawCommandExecutor::executeCommand
     *
     * @expectedException \Xiag\JsonCommand\Exception\RuntimeException
     * @expectedExceptionCode 100
     */
    public function testExecuteCommandWithErrorExecuting()
    {
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
        $commandExecutor = $this->getMockBuilder('Xiag\\JsonCommand\\Executor\\RawCommandExecutor')
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
        $commandExecutor = $this->getMockBuilder('Xiag\\JsonCommand\\Executor\\RawCommandExecutor')
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
