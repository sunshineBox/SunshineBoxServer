<?php
/**
 * Created by PhpStorm.
 * User: shaol
 * Date: 2017/11/30
 * Time: 10:42
 */

function random(): String
{
    $result = null;
    for ($i = 0; $i < 12; $i++) {
        $result .= rand(0, 9);
    }
    return $result;
}

print "激活码生成器给您生成了一个激活码： " . random();