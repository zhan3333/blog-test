<?php
/**
 * 全局方法
 * User: 39096
 * Date: 2017/8/29
 * Time: 22:29
 */

/**
 * 返回json数据
 * @param array $data
 * @param string $msg
 * @param int $code
 * @param bool $exit
 * @return string
 */
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
        header('Content-type: application/json', true); // 加入头信息
        exit($ret);
    } else {
        return $ret;
    }
}

/**
 * 判断登陆状态并返回
 * @param PDO   $db
 * @param int   $uid
 * @return bool
 */
function get_login_status ($db, &$uid) {
    /**
     * 1. 获取头信息中的 uid 和 token
     * 2. 基础判断
     * 3. 查数据库，验证token是否有效
     * 4. 返回登陆状态
     */
    $uid = empty($_SERVER['HTTP_UID'])?null:trim($_SERVER['HTTP_UID']);
    $token = empty($_SERVER['HTTP_TOKEN'])?null:trim($_SERVER['HTTP_TOKEN']);
    if (empty($uid) || empty($token)) {
        $is_login = false;
    } else {
        // 验证token是否正确
        $result = $db->query("select * from `users` where `id` = $uid and `token` = '$token'");
        if ($result) {
            $user = $result->fetch($db::FETCH_ASSOC);

            // 查询不到(token无效)
            if (empty($user)) {
                $is_login = false;
            } else {
                $is_login = true;
            }
        } else {
            $is_login = false;
        }
    }
    return $is_login;
}

/**
 * 获取头信息中的action并返回
 * @return string action
 */
function get_action () {
    return empty($_SERVER['HTTP_ACTION'])?'':trim($_SERVER['HTTP_ACTION']);
}