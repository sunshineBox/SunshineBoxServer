<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2017/12/1
 * Time: 10:51
 */

require_once dirname(__DIR__) . "/utils/RequireUtil.php";

//ini_set("display_errors", "on");

$response_data = entrance();
header('Content-Type: application/json');
print json_encode($response_data);

function entrance(): array
{
    //传入参数不正确，返回402
    if (!isset($_POST['phone_number'])) {
        return returnJson('402');
    }

    if (!isset($_POST['activation_code'])) {
        return returnJson('402');
    }

    //如果数据库连接失败，返回401
    try {
        $db = tryToConnectToTheDatabase();
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    //获取手机号和激活码
    $phone_number = $_POST['phone_number'];
    $activation_code = $_POST['activation_code'];

    try {
        $stmt = $db->prepare("SELECT is_activated from users WHERE phone_number LIKE ? AND activation_code LIKE ?");
        $stmt->execute([(string)$phone_number, (string)$activation_code]);
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    $result = $stmt->fetchAll(PDO::FETCH_NUM);

    if (count($result) == 0) {
        return returnJson('001', 'incorrect');
    } else {
        foreach ($result as $item) {
            if ($item[0] == "true") {
                return returnJson('001', 'expired');
            } else if ($item[0] == "false") {
                try {
                    $stmt = $db->prepare("UPDATE users SET is_activated=\"true\" WHERE phone_number LIKE ?");
                    $stmt->execute([(string)$phone_number]);
                } catch (PDOException $exception) {
                    return returnJson('401');
                }
                try {
                    $stmt = $db->prepare("SELECT uuid FROM users WHERE phone_number LIKE ?");
                    $stmt->execute([(string)$phone_number]);
                } catch (PDOException $exception) {
                    return returnJson('401');
                }
                $result1 = $stmt->fetchAll();
                foreach ($result1 as $item1) {
                    return returnJson('001', 'success', ['uuid' => $item1]);
                }
            } else {
                return returnJson('401');
            }
        }
    }
    return returnJson('401');
}

