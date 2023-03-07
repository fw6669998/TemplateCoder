<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\util;

//获取当前文件的文件夹路径
$dir = dirname(__FILE__);
//获取上层目录
$templateDir = $dir . '/../template';

$root = [];
$res = util::getTemplates($templateDir, $templateDir);

util::response($res);