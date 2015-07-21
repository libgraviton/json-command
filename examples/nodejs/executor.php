<?php
namespace Xiag\JsonCommand\Examples\NodeJs;

use JMS\Serializer\SerializerBuilder;
use Xiag\JsonCommand\Executor\JsonCommandExecutor;

require __DIR__.'/../../vendor/autoload.php';

class Argument
{
    private $key;
    private $value;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }
}

class Result
{
    private $key;
    private $result;

    public function getKey()
    {
        return $this->key;
    }
    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getResult()
    {
        return $this->result;
    }
    public function setResult($result)
    {
        $this->result = $result;
    }
}


$serializer = SerializerBuilder::create()
    ->addDefaultHandlers()
    ->addDefaultSerializationVisitors()
    ->addDefaultDeserializationVisitors()
    ->setDebug(true)
    ->addMetadataDir(__DIR__.'/serializer', 'Xiag\JsonCommand\Examples\NodeJs')
    ->setCacheDir(sys_get_temp_dir())
    ->build();

$executor = new JsonCommandExecutor('node', $serializer, 'array<Xiag\JsonCommand\Examples\NodeJs\Result>');
$executor->addCommandPrefix(__DIR__.'/calculator.js');

/** @var Result[] $results */
$results = $executor->executeCommand([
    new Argument('abc', 1000),
    new Argument('def', 2000),
]);
foreach ($results as $result) {
    printf('Key:    %s' . PHP_EOL, $result->getKey());
    printf('Result: %f' . PHP_EOL, $result->getResult());
}
