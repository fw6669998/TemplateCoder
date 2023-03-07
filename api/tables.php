<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\DB;

$tables = DB::getTables();
$config = DB::getDBConfig();

$tableRes = [];
//id=, type='table',
foreach ($tables as $table) {
    $item['id'] = $table;
    $item['type'] = 'table';
    $item['text'] = $table;
    $tableRes[] = $item;
}
$res['tables'] = $tableRes;
$res['dbs'] = array_keys($config);
//返回json格式响应数据
\src\util::response($res);