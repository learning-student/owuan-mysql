<?php


namespace Owuan\Mysql\Test\Unit;


use Owuan\Mysql\PDO;
use Owuan\Mysql\Test\TestCase;

class PdoTest extends TestCase
{

    protected $attributes = [
        PDO::ATTR_AUTOCOMMIT => true,
        PDO::ATTR_CASE => true,
        PDO::ATTR_CLIENT_VERSION => true,
        PDO::ATTR_CONNECTION_STATUS => true,
        PDO::ATTR_DRIVER_NAME => 'Swoole Style',
        PDO::ATTR_ERRMODE => 'Swoole Style',
        PDO::ATTR_ORACLE_NULLS => 'Swoole Mysql',
        PDO::ATTR_PERSISTENT => 'Swoole Mysql',
        PDO::ATTR_PREFETCH => 'Swoole Mysql',
        PDO::ATTR_SERVER_INFO => 'Swoole Mysql',
        PDO::ATTR_TIMEOUT => -1

    ];

    /**
     * @covers \Owuan\Mysql\PDO::getOptions
     */
    public function testGetOptionsReturnsCorrect()
    {
        $pdo = $this->getPdoInstance();

        $info = $this->getConnectionInfo();
        $info[] = [

        ];
        $response = $this->invokeMethod($pdo, 'getOptions', $info);

        $shouldBe = [
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'root',
            'password' => getenv('MYSQL_PASSWORD', ''),
            'database' => 'owuan_mysql_test',
            'charset' => 'utf8mb4',
            'strict_type' => true,
            'timeout' => -1,
        ];
        $this->assertEquals(
            $shouldBe,
            $response
        );
    }

    public function testDriverNotFound()
    {

        $this->expectException(\InvalidArgumentException::class);

        PDO::checkDriver('psql');


    }

    public function testGetAttributeReturnsCorrect()
    {
        $pdo = $this->getPdoInstance();

        foreach ($this->attributes as $attribute => $value) {
            $this->assertEquals(
                $pdo->getAttribute($attribute),
                $value
            );
        }


        $this->expectException(\InvalidArgumentException::class);

        $pdo->getAttribute(55);

    }


    public function testQuote()
    {
        $pdo = $this->getPdoInstance();


        $output = $pdo->quote('test');


        $this->assertEquals('test', $output);
    }


}