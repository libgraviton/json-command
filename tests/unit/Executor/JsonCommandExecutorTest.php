<?php
namespace Graviton\JsonCommand\UnitTest\Executor;

use Graviton\JsonCommand\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Exception\RuntimeException as SerializerException;
use Graviton\JsonCommand\Executor\JsonCommandExecutor;

/**
 * JsonCommandExecutor class test
 */
class JsonCommandExecutorTest extends TestCase
{
    /**
     * @covers JsonCommandExecutor::executeCommand
     */
    public function testExecuteCommandWithErrorDeserialization()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(200);

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

        /** @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->getMockBuilder('JMS\\Serializer\\SerializerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['serialize', 'deserialize'])
            ->getMock();
        $serializer->expects($this->never())
            ->method('serialize');
        $serializer->expects($this->once())
            ->method('deserialize')
            ->with($rawOutput, 'array', 'json')
            ->willThrowException(new SerializerException());

        /** @var JsonCommandExecutor|\PHPUnit_Framework_MockObject_MockObject $commandExecutor */
        $commandExecutor = $this->getMockBuilder('Graviton\\JsonCommand\\Executor\\JsonCommandExecutor')
            ->setMethods(['createProcess', 'serializeArguments'])
            ->setConstructorArgs(['php', $serializer, 'array'])
            ->getMock();
        $commandExecutor
            ->expects($this->once())
            ->method('createProcess')
            ->with($arguments)
            ->willReturn($process);

        $commandExecutor->executeCommand($arguments);
    }

    /**
     * @covers JsonCommandExecutor::executeCommand
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

        /** @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->getMockBuilder('JMS\\Serializer\\SerializerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['serialize', 'deserialize'])
            ->getMock();
        $serializer->expects($this->never())
            ->method('serialize');
        $serializer->expects($this->once())
            ->method('deserialize')
            ->with($rawOutput, 'array', 'json')
            ->willReturn(__METHOD__);

        /** @var JsonCommandExecutor|\PHPUnit_Framework_MockObject_MockObject $commandExecutor */
        $commandExecutor = $this->getMockBuilder('Graviton\\JsonCommand\\Executor\\JsonCommandExecutor')
            ->setMethods(['createProcess', 'serializeArguments'])
            ->setConstructorArgs(['php', $serializer, 'array'])
            ->getMock();
        $commandExecutor
            ->expects($this->once())
            ->method('createProcess')
            ->with($arguments)
            ->willReturn($process);

        $this->assertEquals(__METHOD__, $commandExecutor->executeCommand($arguments));
    }
}
