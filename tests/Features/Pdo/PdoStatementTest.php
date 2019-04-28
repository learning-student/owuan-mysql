<?php


namespace Owuan\Mysql\Test\Features\Pdo;


use Owuan\Mysql\Test\TestCase;

class PdoStatementTest extends TestCase
{




    public function testBindValue()
    {
        $pdo = $this->getPdoInstance();

        $pdo->exec('create table if not exists  test_table (username varchar (255));');
        $pdo->exec('INSERT INTO test_table set username = "test" ');
        $query = "SELECT * FROM test_table where username = :username";



        $prepare = $pdo->prepare($query);


        $bind = $prepare->bindValue(':username', 'test', \PDO::PARAM_STR);

        $this->assertTrue($bind);

        $response = $prepare->execute();


        $this->assertTrue($response);

        $all = $prepare->fetchAll();

        $this->assertArrayHasKey(0, $all);


    }

    public function testBindValueObject()
    {
        $pdo = $this->getPdoInstance();

        $prepare = $pdo->prepare("SELECT * FROM test_table where username = :username");
        $objectBind  = new class {
            public function __toString()
            {
                return 'test';
            }
        };

        $objectBindCorrect = $prepare->bindValue(':username', $objectBind);

        $this->assertTrue($objectBindCorrect);

        $objectBindFalse = $prepare->bindValue(':username', new class{});

        $this->assertFalse($objectBindFalse);

    }

    public function testBindValueTestNotString()
    {
        $pdo = $this->getPdoInstance();

        $prepare = $pdo->prepare("SELECT * FROM test_table where username = :username");


        $assertFalse = $prepare->bindValue([], []);

        $this->assertFalse($assertFalse);

    }

    public function testBindParam(){
        $pdo = $this->getPdoInstance();


        $query = "SELECT * FROM test_table where username = :username";

        $username = "test";

        $prepare = $pdo->prepare($query);

        $response = $prepare->bindParam(":username", $username);

        $this->assertTrue($response);

        $execute = $prepare->execute();

        $this->assertTrue($execute);

        $data = $prepare->fetch();

        $this->assertArrayHasKey('username', $data);

    }

    public function testBindParamFalse()
    {
        $pdo = $this->getPdoInstance();



        $username = "test";

        $prepare = $pdo->prepare("SELECT * FROM test_table where username = :username");

        $response = $prepare->bindParam([], $username);

        $this->assertFalse($response);
    }

    public function testFetchColumn()
    {
        $pdo = $this->getPdoInstance();


       $prepare = $pdo->prepare("SELECT * FROM test_table where username = :username");

       $prepare->bindValue(":username", "test");

       $prepare->execute();
       $column = $prepare->fetchColumn(0);


       $this->assertNotFalse($column);

    }

    public function testRowCount()
    {
        $pdo = $this->getPdoInstance();
        $prepare = $pdo->prepare("SELECT * FROM test_table where username = ?");

        $prepare->execute([
            'test'
        ]);

        $rowCount = $prepare->rowCount();

        $this->assertGreaterThanOrEqual(0, $rowCount);
    }

}