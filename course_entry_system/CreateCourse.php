<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2018/1/15
 * Time: 15:03
 */

require_once dirname(__DIR__) . "/utils/RequireUtils.php";
ini_set("display_errors", "on");
$response_data = entrance();
header('Content-Type: application/json');
print json_encode($response_data);

function entrance(): array
{
    //传入参数不正确，返回402
    if (!isset($_POST['course_type'])) {
        return returnJson('402');
    }

    if (!isset($_POST['course_name'])) {
        return returnJson('402');
    }

    if (!isset($_POST['course_video'])) {
        return returnJson('402');
    }

    if (!isset($_POST['course_audio'])) {
        return returnJson('402');
    }

    if (!isset($_POST['course_text'])) {
        return returnJson('402');
    }

    //获取传入参数
    $course_type = $_POST['course_type'];
    $course_name = $_POST['course_name'];
    $course_video = $_POST['course_video'];
    $course_audio = $_POST['course_audio'];
    $course_text = $_POST['course_text'];

    //对传入参数的值进行判断
    if ($course_type == "") {
        return returnJson("001", "failure: 'course_type' must has a value!");
    }

    if ($course_name == "") {
        return returnJson("001", "failure: 'course_name' must has a value!");
    }

    //如果数据库连接失败，返回401
    try {
        $db = tryToConnectToTheDatabase();
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    //向数据库中录入数据
    try {
        $result = sqlQuery($db, $course_name, $course_type, $course_video, $course_audio, $course_text);
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    if ($result->rowCount() != 0) {
        return returnJson('001', 'success');
    } else {
        return returnJson('001', 'failure');
    }
}

function sqlQuery(PDO $db, string $course_name, string $course_type, string $course_video, string $course_audio, string $course_text): PDOStatement
{
    if ($course_video == "") {
        $course_video = null;
    }

    if ($course_audio == "") {
        $course_audio = null;
    }

    if ($course_text == "") {
        $course_text = null;
    }
    $stmt = $db->prepare("INSERT INTO courses(course_type,course_name,course_video,course_audio,course_text) VALUES(:course_type,:course_name,:course_video,:course_audio,:course_text)");
    $stmt->bindValue(':course_type', $course_type, PDO::PARAM_STR);
    $stmt->bindValue(':course_name', $course_name, PDO::PARAM_STR);
    $stmt->bindValue(':course_video', $course_video, PDO::PARAM_STR);
    $stmt->bindValue(':course_audio', $course_audio, PDO::PARAM_STR);
    $stmt->bindValue(':course_text', $course_text, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt;
}