<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2017/12/12
 * Time: 15:15
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

    //如果数据库连接失败，返回401
    try {
        $db = tryToConnectToTheDatabase();
    } catch (PDOException $exception) {
        return returnJson('401');
    }

    $course_type = $_POST['course_type'];
    try {
        $stmt = sqlQuery($db, $course_type);
    } catch (PDOException $exception) {
        return returnJson('401');
    } catch (Exception $exception) {
        return returnJson('001', 'failure');
    }

    return returnJson('001', 'success', $stmt->fetchAll());
}

function sqlQuery(PDO $db, string $course_type): PDOStatement
{
    switch ($course_type) {
        case "music":
            $stmt = $db->prepare("SELECT * FROM music_view");
            $stmt->execute();
            return $stmt;
        case "rhymes":
            $stmt = $db->prepare("SELECT * FROM rhymes_view");
            $stmt->execute();
            return $stmt;
        case "game":
            $stmt = $db->prepare("SELECT * FROM game_view");
            $stmt->execute();
            return $stmt;
        case "reading":
            $stmt = $db->prepare("SELECT * FROM reading_view");
            $stmt->execute();
            return $stmt;
        default:
            throw new Exception('There is a problem with the value of $course_type');
    }
}