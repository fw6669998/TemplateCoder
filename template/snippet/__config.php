<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\util;

//需配置参数
$initConfig = [
	"apiPostParam" => "",
];
$cacheConfig = util::getParamCache(__FILE__);
//合并配置
$config = array_merge($initConfig, $cacheConfig);
//响应
util::response(['paramDefine' => $config]);
