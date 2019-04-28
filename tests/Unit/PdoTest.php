<?php


namespace Owuan\Mysql\Test\Unit;


use Owuan\Mysql\Test\TestCase;

class PdoTest extends TestCase
{
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

}