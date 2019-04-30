<?php


namespace Owuan\Mysql\Test\Unit;


use Owuan\Mysql\PDO;
use Owuan\Mysql\Test\TestCase;

class PDOStatementTest extends TestCase
{


    public function testSetFetchMode() : void
    {
        $pdo = $this->getPdoInstance();


        $pdo->exec("create table if not exits owuan_test(id int)");

        $statement = $pdo->prepare("SELECT * FROM owuan_test ");


        $statement->setFetchMode(PDO::FETCH_ASSOC);

        $pdo->exec("drop table owuan_test");

        $this->assertEquals(PDO::FETCH_ASSOC, $statement->fetchStyle);
    }
}