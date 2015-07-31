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


// first calculator
$firstCalculatorResultObject = $this->container->get('json_calculator.first')
    ->calculate(new FirstCalculatorArg('some data'));

// second calculator
$rawArray = $this->container->get('json_calculator.second')
    ->executeCommand('some data');
