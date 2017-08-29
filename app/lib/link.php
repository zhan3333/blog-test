<?php
/**
 * 创建一个数据库连接
 * User: 39096
 * Date: 2017/8/26
 * Time: 22:31
 */

$config = require __DIR__ . '/../config/database.php';

try {
    // mysql: 告诉pdo我们连接的数据库是什么类型的
    // host: 告诉主机地址
    // dbname: 选择数据库
    // username: 用户名
    // passwd: 密码
    $db = new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", $config['username'], $config['password']);
} catch (PDOException $PDOException) {
    echo $PDOException->getMessage() . PHP_EOL;
    exit();
}