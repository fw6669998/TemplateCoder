<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\util;

$template = $_GET['template'];
$params = $_GET['params'];
$paramFile = $_SERVER['DOCUMENT_ROOT'] . '/template/' . $template . '/__config.php.json';
$content = json_encode($params);
util::saveFile($paramFile, $content);
util::response($params);