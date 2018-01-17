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
    if (!isset($_POST['uuid'])) {
        return returnJson('402');
    }

    //获取传入参数
    $uuid = $_POST['uuid'];

    //对传入参数的值进行判断
    if ($uuid == "") {
        return returnJson('001', "failure: 'uuid' must has a value!");
    }

    //如果数据库连接失败，返回401
    try {
        $db = tryToConnectToTheDatabase();
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    try {
        $result = sqlQuery($db, $uuid);
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    if ($result->rowCount() != 0) {
        return returnJson('001', 'success');
    } else {
        return returnJson('001', 'failure');
    }
}

function sqlQuery(PDO $db, string $uuid)
{
    $stmt = $db->prepare("DELETE FROM users WHERE uuid=?");
    $stmt->execute([$uuid]);
    return $stmt;
}