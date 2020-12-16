<?php
/**
 * Created by PhpStorm.
 * User: hw201902
 * Date: 2020/12/14
 * Time: 10:01
 */
require_once "MysqlDB.php";




class MysqlPool
{
    private static $instance;
    private $pool;
    private $config;
    private $pool_get_timeout;

    /**
     * 获取mysql进程池单例
     * @param null $config
     * @return MysqlPool
     */
    public static function getInstance($config = null)
    {
        if (empty(self::$instance)) {
            if (empty($config)) {
                throw new RuntimeException("mysql config is empty");
            }
            self::$instance = new static($config);
        }
        return self::$instance;
    }

    public function __construct($config)
    {
        if (empty($this->pool)) {
            $this->config = $config;
            $this->pool = new \Swoole\Coroutine\Channel($config['pool_size']);
            for ($i = 0; $i < $config['pool_size']; $i++) {
                \go(function() use ($config) {
                    $mysql = new MysqlDB();
                    $res = $mysql->connect($config['mysql']);
                    if ($res === false) {
                        throw new RuntimeException("Failed to connect mysql server");
                    } else {
                        $this->pool->push($mysql);
                    }
                });
            }
        }
    }

    public function get()
    {
        if ($this->pool->length() > 0) {
            $mysql = $this->pool->pop($this->config['pool_get_timeout']);
            if (false === $mysql) {
                throw new RuntimeException("Pop mysql timeout");
            }
            return $mysql;
        } else {
            throw new RuntimeException("Pool length <= 0");
        }
    }

    public function recycle(MysqlDB $mysql){
        $this->pool->push($mysql);
    }

    /**
     * 获取连接池长度
     * @return mixed
     */
    public function getPoolSize(){
        return $this->pool->length();
    }
}
