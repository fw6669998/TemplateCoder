<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\DB;
use src\JavaUtil;
use src\StringUtil;
use src\util;
use src\Param;

//获取表名
$table = util::param('table');
//定义和获取模板参数
$basePackage = util::param('basePackage');
$savePath = util::param('baseSavePath') . '\\' . JavaUtil::$packageEntity;
$package = $basePackage . '.' . JavaUtil::$packageEntity;

$cols = DB::getColumnInfos($table);
//通过表名获取类名
$className = StringUtil::upper_AndFirst($table);
//添加保存文件位置到响应头
util::setSavePath($savePath, 'Excel'.$className . '.java');
?>
package <?=$package?>;

import lombok.Getter;
import lombok.Setter;
import com.alibaba.excel.annotation.ExcelProperty;
{{imports}}

{{classAnnotation}}
@Getter
@Setter
public class <?= 'Excel'.$className ?>{

<? foreach ($cols as $col => $info) { ?>
    <?='@ExcelProperty(value = "'.$info->getComment().'")'.PHP_EOL?>
    private <?= JavaUtil::getJavaType($info) ?> <?= $col ?>;

<? } ?>

}
<?= StringUtil::replacePlaceHolder('{{imports}}', JavaUtil::getImport()) //参数2的内容最后会替换行首{{imports}}的内容 ?>
<?= StringUtil::replacePlaceHolder('{{classAnnotation}}', JavaUtil::getClassAnnotationStr()) ?>
