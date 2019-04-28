<?php

namespace Owuan\Mysql\Test;

class TestCase extends \PHPUnit\Framework\TestCase
{

    protected function getConnectionInfo()
    {
        return [
            'mysql:host=127.0.0.1;dbname=owuan_mysql_test',
            'root',
            'sanane123'
        ];
    }

}