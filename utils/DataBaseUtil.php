<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2017/11/29
 * Time: 22:23
 */

require_once dirname(__DIR__) . "/data/DataBase.php";
require_once dirname(__DIR__) . "/utils/JsonUtil.php";

//如果数据库连接失败，返回401
function tryToConnectToTheDatabase(): PDO
{
    //connect to database 'mealplan'
    $db = new PDO(MYSQL_DSN, MYSQL_USERNAME, MYSQL_PASSWORD);
    //if the sql-running fails, throw the exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //return rows in obj
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    return $db;
}

