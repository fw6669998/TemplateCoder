<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\DB;
use src\JavaUtil;
use src\StringUtil;
use src\util;

//获取表名
$table = util::param('table');
//定义和获取模板参数
$basePackage = util::param('basePackage');
$savePath = util::param('baseSavePath') . '\\' . JavaUtil::$packageEntity;
$package = $basePackage . '.' . JavaUtil::$packageEntity;
$isExcelEntity = util::param('entityExcel');

$cols = DB::getColumnInfos($table);
//通过表名获取类名
$className = StringUtil::upper_AndFirst($table). 'Id';
//添加保存文件位置到响应头
if (DB::getIdColumns($table)>1){
    util::setSavePath($savePath, $className . '.java');
}
?>
package <?=$package?>;

import javax.persistence.Column;
import javax.persistence.Id;
import java.io.Serializable;
{{imports}}

public class <?= $className ?> implements Serializable {

<? foreach ($cols as $col => $info) { if (!$info->isPrimaryKey()) continue; ?>
<?= $info->getComment() ? "    //" . $info->getComment() . PHP_EOL : '' ?>
    @Id
    <?='@Column(name = "'.$info->getName().'")'.PHP_EOL?>
    private <?= JavaUtil::getJavaType($info) ?> <?= $col ?>;

<? } ?>
<? foreach ($cols as $col => $info) { if (!$info->isPrimaryKey()) continue; ?>
    public <?= JavaUtil::getJavaType($info) ?> get<?= ucfirst($col); ?>(){
        return this.<? echo $col; ?>;
    }

    public void set<? echo ucfirst($col) . '(' . JavaUtil::getJavaType($info) . ' ' . $col; ?>){
        this.<? echo $col . '=' . $col; ?>;
    }

<? } ?>
}
<?= StringUtil::replacePlaceHolder('{{imports}}', JavaUtil::getImport()) //参数2的内容最后会替换行首{{imports}}的内容 ?>
