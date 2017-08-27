<?php
/**
 * 用户接口
 * User: 39096
 * Date: 2017/8/26
 * Time: 22:26
 */

// 注册
// 登陆

require_once __DIR__ . '/../lib/link.php';

// 获取接口名称
//$action = empty($_SERVER['action'])?'':trim($_SERVER['action']);
$action = empty($_POST['action'])?'':trim($_POST['action']);

function json ($data = [], $msg = 'success', $code = 0, $exit = true) {
    $ret = json_encode(
        [
            'data' => $data,
            'msg' => $msg,
            'code' => $code
        ],
        JSON_UNESCAPED_UNICODE
    );
    if ($exit) {
        exit($ret);
    } else {
        return $ret;
    }
}

// 执行操作
if ($action == 'register') {
    // 注册操作
    // todo 数据验证...

    // 获取表单数据k
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];

    // todo 验证用户名是否被使用

    // todo password 处理成密文

    $result = $db->exec("insert into users values (null, '$user_name', '$password', null, null)");
    if ($result == 1) {
        return json([], '注册成功');
    } else {
        return json([$db->errorInfo()], '注册失败', -1);
    }
} elseif ($action == 'login') {
    // 登陆操作
}