<?php

namespace Owuan\Mysql\Test;

use Owuan\Mysql\PDO;

class TestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    protected function getPdoInstance() : PDO
    {
        return new PDO(...$this->getConnectionInfo());
    }

    protected function getConnectionInfo()
    {

        $password = getenv('MYSQL_PASSWORD', '');

        return [
            'mysql:host=127.0.0.1;dbname=owuan_mysql_test',
            'root',
            $password
        ];
    }

}