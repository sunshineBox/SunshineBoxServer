<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2017/11/29
 * Time: 22:08
 */

require_once dirname(__DIR__) . "/data/DataBase.php";
require_once dirname(__DIR__) . "/utils/DataBaseUtil.php";

$response_data = entrance();
header('Content-Type: application/json');
print json_encode($response_data);

//main
function entrance(): array
{
    //传入参数不正确，返回402
    if (!isset($_POST['phone_number'])) {
        return returnJson('402');
    }

    //如果数据库连接失败，返回401
    try {
        $db = tryToConnectToTheDatabase();
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    //获取手机号
    $phone_number = $_POST['phone_number'];
    try {
        $stmt = $db->prepare("SELECT phone_number FROM users WHERE phone_number LIKE ? AND is_activated LIKE ?");
        $stmt->execute([(string)$phone_number, "false"]);
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    if ($stmt->fetch() == false) {
        return returnJson("001", "对不起，该手机号没有激活权限或已经激活");
    } else {
        return returnJson("001", "该手机号可以激活");
    }

}


