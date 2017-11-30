<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2017/11/29
 * Time: 22:08
 */

//ini_set("display_errors", "on");

use SunshineBoxServer\activation_system\SendShortMessageUtils;

require_once dirname(__DIR__) . "/utils/DataBaseUtil.php";
require_once __DIR__ . "/SendShortMessageUtils.php";

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
        return returnJson("001", "failure");
    } else {
        //生成4位随机数作为验证码
        $captcha = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

        //向阿里云发送：1.手机号 2.验证码
        $response = SendShortMessageUtils::sendSms(
            $phone_number,
            Array(
                "code" => $captcha
            )
        );

        $creation_time = time();

        if ($response->Message == 'OK') {
            try {
                $stmt = $db->prepare('INSERT INTO captcha_cache (phone_number,captcha,creation_time) VALUES (?,?,?)');
                $stmt->execute([$phone_number, $captcha, $creation_time]);
                return returnJson('001', "success");
            } catch (PDOException $exception) {
                return returnJson('401');
            }
        } else {
            return returnJson('401');
        }
    }
}
