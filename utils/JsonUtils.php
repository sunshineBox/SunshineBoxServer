<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2017/11/29
 * Time: 22:17
 * @param String $flag
 * @param String|null $message
 * @param array|null $content
 * @return array
 */

function returnJson(String $flag, String $message = null, Array $content = null): array
{
    return ['flag' => $flag, 'message' => $message, 'content' => $content];
}