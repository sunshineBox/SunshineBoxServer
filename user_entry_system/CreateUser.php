<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2018/1/17
 * Time: 16:48
 */

require_once dirname(__DIR__) . "/utils/RequireUtils.php";
ini_set("display_errors", "on");
$response_data = entrance();
header('Content-Type: application/json');
print json_encode($response_data);

function entrance(): array
{
    //传入参数不正确，返回402
    if (!isset($_POST['phone_number'])) {
        return returnJson('402');
    }

    //获取传入参数
    $phone_number = $_POST['phone_number'];


    //对传入参数的值进行判断
    if ($phone_number == "") {
        return returnJson("001", "failure: 'phone_number' must has a value!");
    }

    //如果数据库连接失败，返回401
    try {
        $db = tryToConnectToTheDatabase();
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    //向数据库中录入数据
    try {
        $result = sqlQuery($db, $phone_number);
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    if ($result->rowCount() != 0) {
        return returnJson('001', 'success');
    } else {
        return returnJson('001', 'failure');
    }
}

function sqlQuery(PDO $db, string $phone_number): PDOStatement
{
    $stmt = $db->prepare("INSERT INTO users(phone_number,is_activated) VALUES(:phone_number,:is_activated)");
    $stmt->bindValue(':phone_number', $phone_number, PDO::PARAM_STR);
    $stmt->bindValue(':is_activated', "false", PDO::PARAM_STR);
    $stmt->execute();
    return $stmt;
}