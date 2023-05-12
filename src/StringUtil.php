<?php

namespace src;

class StringUtil
{

    public static $sep = '--------------------------division--------------------------';

    public static function upper_($str)
    {
        $arr = explode('_', $str);
        $res = '';
        for ($i = 0; $i < count($arr); $i++) {
            if ($i == 0) {
                $res .= $arr[$i];
            } else {
                $res .= ucfirst($arr[$i]);
            }
        }
        return $res;
    }

    public static function upper_AndFirst($str)
    {
        $arr = explode('_', $str);
        $res = '';
        for ($i = 0; $i < count($arr); $i++) {
            $res .= ucfirst($arr[$i]);
        }
        return $res;
    }

    /**
     * @param $arr
     * @return MyColumn[]
     */
    public static function colsUpper_($arr)
    {
        $arr2 = [];
        foreach ($arr as $key => $value) {
            $arr2[self::upper_($key)] = $value;
        }
        return $arr2;
    }

    /**
     * @param $placeholder string 占位符
     * @param $replaceContent string 替换内容
     * @return string
     */
    public static function replacePlaceHolder($placeholder, $replaceContent)
    {
        $res = self::$sep . $placeholder . PHP_EOL . $replaceContent;
        return $res;
    }

}