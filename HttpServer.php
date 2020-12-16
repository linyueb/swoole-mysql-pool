<?php
/**
 * Created by PhpStorm.
 * User: hw201902
 * Date: 2020/12/14
 * Time: 10:07
 */
//多进程管理模块
require_once "MysqlPool.php";
\Co\run(function () {
    $server = new \Co\Http\Server("0.0.0.0", 9501, false);
    $pool = MysqlPool::getInstance([
        'pool_size'=>5,
        'pool_get_timeout'=>1,
        'timeout'=>1,
        'charset'=>'utf8',
        'strict_type'=>false,
        'fetch_mode'=>true,
        'mysql'=>[
            'host'=>'127.0.0.1',
            'port'=>'3306',
            'user'=>'homestead',
            'password'=>'secret',
            'database'=>'blog',
        ]
    ]);
    $server->handle('/', function ($request, $response) use ($pool){
        $mysql = $pool->get();
        $res = $mysql->query("select id,phone,username from user limit 1");
        var_dump($res);
        $pool->recycle($mysql);
        $response->end("<h1>Test</h1>");
    });
    $server->handle('/test', function ($request, $response) {
        $response->end("<h1>Test</h1>");
    });
    $server->handle('/stop', function ($request, $response) use ($server) {
        $response->end("<h1>Stop</h1>");
        $server->shutdown();
    });
    $server->start();
});
