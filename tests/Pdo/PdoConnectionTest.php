<?php


namespace Owuan\Mysql\Test;


use function foo\func;
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
        [$dsn, $username, $password] = $this->getConnectionInfo();

        $pdo = new PDO($dsn, $username, $password);

        $this->assertInstanceOf(PDO::class, $pdo);
    }
}