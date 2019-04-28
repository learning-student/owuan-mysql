<?php


namespace Owuan\Mysql\Test;


use Owuan\Mysql\ConnectionException;
use Owuan\Mysql\PDO;

class PdoConnectionTest extends TestCase
{


    public function testPdoCantConnectWithCorrectInfo()
    {
        $this->expectException(ConnectionException::class);

        list($dsn, $username, $password) = $this->getConnectionInfo();

        new PDO($dsn, $username, $password);

    }


    public function testPdoCanConnectWithCorrectInfo()
    {
        list($dsn, $username, $password) = $this->getConnectionInfo();




        new PDO($dsn, $username, $password);

    }
}