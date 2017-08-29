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
$action = empty($_SERVER['HTTP_ACTION'])?'':trim($_SERVER['HTTP_ACTION']);
$uid = empty($_SERVER['HTTP_UID'])?null:trim($_SERVER['HTTP_UID']);
$token = empty($_SERVER['HTTP_TOKEN'])?null:trim($_SERVER['HTTP_TOKEN']);

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
        header('Content-type: application/json', true);
        exit($ret);
    } else {
        return $ret;
    }
}

// 执行操作
if ($action == 'register') {
    // 注册操作

    // 获取表单数据k
    $user_name = empty($_POST['user_name'])?null:trim($_POST['user_name']);
    $password = empty($_POST['password'])?null:trim($_POST['password']);
    if (empty($user_name)) return json([], '用户名必须输入', -1);
    if (empty($password)) return json([], '密码必须输入', -1);
    if (!is_string($user_name) || strlen($user_name) < 6) return json([], '用户名不合法', -1);
    if (!is_string($password) || strlen($password) < 6) return json([], '密码不合法', -1);

    // 验证用户名是否被使用
    $result = $db->query("select * from users where `user_name` = '$user_name'");
    if ($result->fetchObject()) return json([], '用户名已被使用', -1);

    // password 处理成密文
    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $result = $db->exec("insert into users (`user_name`, `password`) values ('$user_name', '$hash_password')");

    if ($result == 1) {
        return json([], '注册成功');
    } else {
        return json([$db->errorInfo()], '注册失败', -1);
    }
} elseif ($action == 'login') {
    // 登陆操作

    // 过滤数据
    $user_name = empty($_POST['user_name'])?null:trim($_POST['user_name']);
    $password = empty($_POST['password'])?null:trim($_POST['password']);
    if (empty($user_name)) return json([], '用户名必须输入', -1);
    if (empty($password)) return json([], '密码必须输入', -1);

    // 查询用户名
    $result = $db->query("select * from `users` where `user_name` = '$user_name'");
    $user = $result->fetchObject();
    if (empty($user)) return json([], '用户名不存在', -1);
    $hash_password = $user->password;
    $user_id = $user->id;

    // 验证密码
    if (password_verify($password, $hash_password)) {
        // 密码正确, 创建token写入数据库，并返回
        $token = password_hash($password, PASSWORD_DEFAULT);
        $affect = $db->exec("update users set `token` = '$token' where `id` = $user_id");
        if ($affect < 1) return json([], '创建token失败', -1);
        return json(['token' => $token, 'user_id' => $user_id], '登陆成功');
    } else {
        // 密码错误
        return json([], '用户名或密码错误', -1);
    }
} elseif ($action == 'get_self_info') {
    // 获取自己的信息

    if (empty($uid) || empty($token)) return json([], '未登录', -1);
    // 验证token是否正确
    $result = $db->query("select * from `users` where `id` = $uid and `token` = '$token'");
    if ($result) {
        $user = $result->fetch($db::FETCH_ASSOC);

        // 查询不到
        if (empty($result)) return json([], '未登录', -1);

        // 查询到了
        unset($user['password'], $user['token']);
        return json(['item' => $user]);
    } else {
        return json([$db->errorInfo()], '数据库错误', -1);
    }

} elseif ($action == 'get_user_info') {
    // 根据user_id 获取用户信息
    $user_id = empty($_POST['user_id'])?null:trim($_POST['user_id']);
    if (empty($user_id)) return json([], 'user_id必须传入', -1);
    if (!is_numeric($user_id)) return json([], 'user_id必须为参数', -1);

    // 执行查询
    $result = $db->query("select `id`, `user_name`, `reg_time` from `users` where `id` = $user_id");
    if ($result) {
        $user = $result->fetch($db::FETCH_ASSOC);
        return json(['item' => $user]);
    } else {
        return json([$db->errorInfo()], '数据库错误', -1);
    }
} else {
    return json([$_SERVER], '无效的接口', -1);
}