<?php


namespace Owuan\Mysql\Test\Features\Pdo;


use Owuan\Mysql\ConnectionException;
use Owuan\Mysql\PDO;
use Owuan\Mysql\Test\TestCase;
use Swoole\Coroutine\Mysql;

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


    public function testPdoReturnsCorrectInstance()
    {
        $pdo = $this->getPdoInstance();


        $this->assertInstanceOf(Mysql::class, $pdo->getClient());

    }


    public function testPdoClientCouldntConnect()
    {
        $pdo = $this->getPdoInstance();

        $mock = \Mockery::mock(Mysql::class);

        $mock->shouldReceive('connect')
            ->andReturn(false)
            ->once()
            ->andSet('connected', false);

        $mock->shouldReceive('close')
            ->andReturn(null);



        $pdo->setClient($mock);


        $this->expectException(ConnectionException::class);

        $this->invokeMethod($pdo, 'connect');

    }
}
