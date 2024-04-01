<?php
//$baseDir = 'D:\temp\java\\';
//ini_set('display_errors', true);
//error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\StringUtil;
use src\util;
use src\Param;

$baseDir = Param::$baseDir;

$host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
$table = $_POST['table'];
$template = $_POST['template'];
$param = $_POST['params'] ? $_POST['params'] : [];
//判断是否是文件夹
if (strpos($template, '.php') === false) {
    return;
}
$resData = [];
$resData['table'] = $table;
$resData['template'] = $template;
//获取请求主机
$url = $host . 'template/' . $template;
//添加请求参数
$param['table'] = $table;
$param['__db'] = $_POST['__db'];
$ch = curl_init($url . "?" . http_build_query($param));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
//获取响应体
$content = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
//获取文件路径
$filepath = util::getFilePathFromHeader($response, $ch);
$arr = explode(StringUtil::$sep, $content);
$content1 = $arr[0];
//替换占位符
for ($i = 1; $i < count($arr); $i++) {
    $str = $arr[$i];
    //匹配$str文本中的第一个{{xxx}}字符
    preg_match('/{{(.*?)}}/', $str, $matches);
    if ($matches[0]) {
        $str = str_replace($matches[0] . PHP_EOL, '', $str);
        $content1 = str_replace($matches[0], $str, $content1);
    }
}
$fileSaveInfo = $filepath;
$saveRes = false;
if ($filepath) {
    try {
        $saveRes = util::saveFile($filepath, $content1);
    } catch (Exception $e) {
        $fileSaveInfo = '保存文件失败,' . $e->getMessage();
    }
}
if (!$saveRes) {
    $fileSaveInfo = '未保存到文件,' . $filepath;
}

$resData['path'] = $fileSaveInfo;
//html实体转换
$resData['content'] = htmlentities($content1);

//print_r($tables);
//print_r($templates);
//exit();

//返回结果
util::response($resData);