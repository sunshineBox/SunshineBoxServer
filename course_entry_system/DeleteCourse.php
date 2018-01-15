<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2018/1/15
 * Time: 15:04
 */

require_once dirname(__DIR__) . "/utils/RequireUtils.php";
ini_set("display_errors", "on");
$response_data = entrance();
header('Content-Type: application/json');
print json_encode($response_data);

function entrance(): array
{
    //传入参数不正确，返回402
    if (!isset($_POST['course_id'])) {
        return returnJson('402');
    }

    //获取传入参数
    $course_id = $_POST['course_id'];

    //对传入参数的值进行判断
    if ($course_id == "") {
        return returnJson('001', "failure: 'course_id' must has a value!");
    }

    //如果数据库连接失败，返回401
    try {
        $db = tryToConnectToTheDatabase();
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    try {
        $result = sqlQuery($db, $course_id);
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    if ($result->rowCount() != 0) {
        return returnJson('001', 'success');
    } else {
        return returnJson('001', 'failure');
    }
}

function sqlQuery(PDO $db, string $course_id)
{
    $stmt = $db->prepare("DELETE FROM courses WHERE course_id=?");
    $stmt->execute([$course_id]);
    return $stmt;
}