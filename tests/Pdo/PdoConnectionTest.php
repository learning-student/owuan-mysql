<?php


namespace Owuan\Mysql\Test;


use Owuan\Mysql\ConnectionException;
use Owuan\Mysql\PDO;

class PdoConnectionTest extends TestCase
{


    public function testPdoCantConnectWithCorrectInfo()
    {
        $this->expectException(ConnectionException::class);

        [$dsn, $username, $password] = $this->getConnectionInfo();

        new PDO($dsn, "", $password);

    }


    public function testPdoCanConnectWithCorrectInfo()
    {

        go(function (){
            [$dsn, $username, $password] = $this->getConnectionInfo();

            new PDO($dsn, $username, $password);
        });
    }
}