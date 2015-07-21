<?php
namespace Xiag\JsonCommand\Examples\Symfony;

use Xiag\JsonCommand\Executor\JsonCommandExecutor;

class FirstCalculator
{
    /**
     * @var JsonCommandExecutor
     */
    private $executor;

    /**
     * @param JsonCommandExecutor $executor
     */
    public function __construct(JsonCommandExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param FirstCalculatorArg $arg
     * @return FirstCalculatorResult
     */
    public function calculate(FirstCalculatorArg $arg)
    {
        return $this->executor->executeCommand([$arg]);
    }
}
class FirstCalculatorArg
{
}
class FirstCalculatorResult
{
}

class SecondCalculator
{
    /**
     * @var JsonCommandExecutor
     */
    private $executor;

    /**
     * @param JsonCommandExecutor $executor
     */
    public function __construct(JsonCommandExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param SecondCalculatorArg[] $args
     * @return SecondCalculatorResult
     */
    public function calculate(array $args)
    {
        return $this->executor->executeCommand($args);
    }
}
class SecondCalculatorArg
{
}
class SecondCalculatorResult
{
}