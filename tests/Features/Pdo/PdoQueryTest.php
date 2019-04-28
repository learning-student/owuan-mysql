<?php


namespace Owuan\Mysql\Test\Features\Pdo;


use Owuan\Mysql\PDOStatement;
use Owuan\Mysql\QueryException;
use Owuan\Mysql\Test\TestCase;

class PdoQueryTest extends TestCase
{

    protected static $queriesToRun = [
        "create table IF NOT EXISTS testtable(username varchar(255) not null);",
        "insert into testtable set username='deneme';",
        "select * from testtable",
        "delete from testtable where username='test'",
        "update testtable set username='aaaa'",
        "drop table testtable;",
    ];

    protected static $prepareQueriesToRun = [
        "create table IF NOT EXISTS testtable(username varchar(255) not null);" => [],

        "insert into testtable set username=?;" => [
            'test'
        ],

        "delete from testtable where username=:username" => [
            ':username' => 'test'
        ],

        "drop table testtable;" => []
    ];


    public function testQueriesToRunExecutesSuccessfully(): void
    {
        $pdo = $this->getPdoInstance();

        foreach (static::$queriesToRun as $item) {
            $this->assertNotFalse($pdo->query($item));
        }
    }

    public function testPrepareAndExecute()
    {
        $pdo = $this->getPdoInstance();

        foreach (static::$prepareQueriesToRun as $item => $value) {
            /**
             * @var PDOStatement $prepare
             */

            $prepare = $pdo->prepare($item);

            $this->assertInstanceOf(PDOStatement::class, $prepare);

            $response = $prepare->execute($value);

            $this->assertNotFalse($response);
        }
    }


    public function testTransactionCommit()
    {
        $pdo = $this->getPdoInstance();

        $pdo->beginTransaction();

        $this->assertTrue($pdo->inTransaction());

        foreach (static::$queriesToRun as $item) {
            $response = $pdo->query($item);
            $this->assertNotFalse($response);
        }

        $response = $pdo->commit();

        $this->assertTrue($response);
    }

    public function testTransactionRollback()
    {
        $pdo = $this->getPdoInstance();

        $pdo->beginTransaction();

        foreach (static::$queriesToRun as $item) {
            $response = $pdo->query($item);

            $this->assertNotFalse($response);
        }

        $response = $pdo->rollBack();

        $this->assertTrue($response);
    }

    public function testGetLastInsertId()
    {
        $pdo = $this->getPdoInstance();


        $this->assertTrue($pdo->query(static::$queriesToRun[0]));
        $this->assertTrue($pdo->query(static::$queriesToRun[1]));

        $response = $pdo->lastInsertId();
        $this->assertGreaterThanOrEqual(0, $response);

    }

    public function testQueryWithWrongSql()
    {
        $pdo = $this->getPdoInstance();

        $this->expectException(QueryException::class);

        $pdo->query('SELEC * FROM testtable');
    }


    public function testPrepareWithWrongSql()
    {
        $pdo = $this->getPdoInstance();

        $this->expectException(QueryException::class);

        $pdo->prepare('SELEC * FROM testtable');
    }

    public function testQuote()
    {
        $pdo = $this->getPdoInstance();

        $this->expectException(\BadMethodCallException::class);

        $pdo->quote('test');
    }

}