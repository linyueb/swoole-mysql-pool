<?php
/**
 * Created by PhpStorm.
 * User: hw201902
 * Date: 2020/12/14
 * Time: 10:02
 */



class MysqlDB
{
    private $connection;


    public function connect($config)
    {
        $connection = new \Swoole\Coroutine\MySQL();
        $res = $connection->connect($config);
        if ($res === false) {
            throw new RuntimeException($connection->connect_error, $connection->errno);
        } else {
            $this->connection = $connection;
        }
        return $res;
    }


    public function query($sql){
        $result = $this->connection->query($sql);
        return $result;
    }
}
