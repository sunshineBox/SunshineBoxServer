<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2018/1/17
 * Time: 16:47
 */

require_once dirname(__DIR__) . "/utils/RequireUtils.php";
$response_data = entrance();
header('Content-Type: application/json');
print json_encode($response_data);

function entrance(): array
{
    //如果数据库连接失败，返回401
    try {
        $db = tryToConnectToTheDatabase();
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    //查询数据库
    try {
        $check_all_courses = sqlQuery($db);
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    return returnJson('001', 'success', $check_all_courses->fetchAll());
}

function sqlQuery(PDO $db): PDOStatement
{
    $stmt = $db->prepare("SELECT * FROM users");
    $stmt->execute();
    return $stmt;
}