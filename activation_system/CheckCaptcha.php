<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2017/11/30
 * Time: 11:24
 */

require_once dirname(__DIR__) . "/utils/RequireUtil.php";

$response_data = entrance();
header('Content-Type: application/json');
print json_encode($response_data);

function entrance(): array
{
    //传入参数不正确，返回402
    if (!isset($_POST['phone_number'])) {
        return returnJson('402');
    }

    if (!isset($_POST['captcha'])) {
        return returnJson('402');
    }

    //如果数据库连接失败，返回401
    try {
        $db = tryToConnectToTheDatabase();
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    //获取手机号和验证码
    $phone_number = $_POST['phone_number'];
    $captcha = $_POST['captcha'];

    try {
        $stmt = $db->prepare("SELECT creation_time FROM captcha_cache WHERE phone_number LIKE ? AND captcha LIKE ?");
        $stmt->execute([(string)$phone_number, (string)$captcha]);
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    $result = $stmt->fetchAll(PDO::FETCH_NUM);


    if (count($result) == 0) {
        //手机号与验证码不匹配
        return returnJson('001', 'incorrect');
    } else {
        $now_time = time();

        foreach ($result as $item) {
            if (($now_time - (int)$item[0]) > 300) {
                //验证码过期
                return returnJson('001', 'expired');
            } else {
                //删除缓存数据库captcha_cache中全部与phone_number相关的行
                try {
                    $stmt = $db->prepare("DELETE FROM captcha_cache WHERE phone_number LIKE ?");
                    $stmt->execute([$phone_number]);
                } catch (PDOException $e) {
                    return returnJson('401');
                }

                //更改数据库中的is_activated="true"
                try {
                    $stmt = $db->prepare("UPDATE users SET is_activated=\"true\" WHERE phone_number LIKE ?");
                    $stmt->execute([$phone_number]);
                } catch (PDOException $e) {
                    return returnJson('401');
                }

                //查询数据库中的uuid
                try {
                    $stmt = $db->prepare("SELECT uuid FROM users WHERE phone_number LIKE ?");
                    $stmt->execute([$phone_number]);
                } catch (PDOException $e) {
                    return returnJson('401');
                }

                $result1 = $stmt->fetchAll(PDO::FETCH_NUM);

                if (count($result1) == 0) {
                    return returnJson('401');
                } else {
                    foreach ($result1 as $value) {
                        return returnJson('001', $value[0]);
                    }
                }
            }
        }
    }
    return returnJson('401');
}