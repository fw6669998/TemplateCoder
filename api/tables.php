<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\DB;

$tableRes = [];
$config = [];
try {
    $config = DB::getDBConfig();
    $tables = DB::getTables();
    //id=, type='table',
    foreach ($tables as $table) {
        $item['id'] = $table;
        $item['type'] = 'table';
        $item['text'] = $table;
        $tableRes[] = $item;
    }
} catch (Exception $e) {
    //
}

$res['tables'] = $tableRes;
$res['dbs'] = array_keys($config);
//返回json格式响应数据
\src\util::response($res);