<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2017/11/29
 * Time: 13:56
 */

use SunshineBoxServer\activation_system\SendShortMessageUtils;

require_once __DIR__ . "/SendShortMessageUtils.php";

$phone_number = $_POST['phone_number'];
$verification_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
$response = SendShortMessageUtils::sendSms(
    $phone_number,
    Array(
        "code" => $verification_code
    )
);