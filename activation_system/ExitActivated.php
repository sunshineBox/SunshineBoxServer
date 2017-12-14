<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2017/12/8
 * Time: 16:54
 */

require_once dirname(__DIR__) . "/utils/RequireUtils.php";
//ini_set("display_errors", "on");

$response_data = entrance();
header('Content-Type: application/json');
print json_encode($response_data);

function entrance(): array
{
    //传入参数不正确，返回402
    if (!isset($_POST['uuid'])) {
        return returnJson('402');
    }

    //如果数据库连接失败，返回401
    try {
        $db = tryToConnectToTheDatabase();
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    $uuid = $_POST['uuid'];

    try {
        $stmt = sqlQuery($db, $uuid);
    } catch (PDOException $exception) {
        return returnJson('401');
    }


    $count = $stmt->rowCount();

    if ($count == 1) {
        return returnJson("001", "success");
    } else {
        return returnJson("001", "failure");
    }
}

function sqlQuery(PDO $db, string $uuid): PDOStatement
{
    $stmt = $db->prepare("UPDATE users SET is_activated=\"false\" WHERE uuid LIKE ?");
    $stmt->execute([(string)$uuid]);
    return $stmt;
}