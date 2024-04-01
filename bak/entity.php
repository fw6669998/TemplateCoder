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
$savePath = util::param('baseSavePath');
//从路径中获取包名
$tempPath=str_replace('\\','.',$savePath);
$tempPath=str_replace('/','.',$tempPath);
//查找src\main\java\后面的包名
$package=substr($tempPath,strpos($tempPath,'src.main.java.')+14);
//去掉最后的.
$package=rtrim($package,'.');
$isExcelEntity = util::param('entityExcel');

$cols = DB::getColumnInfos($table);
//添加需要导入的类
JavaUtil::addImport('javax.persistence.*');
JavaUtil::addImport('java.util.Objects');
//通过表名获取类名
$className = StringUtil::upper_AndFirst($table);
//添加保存文件位置到响应头
util::setSavePath($savePath, $className . '.java');
?>
package <?=$package?>;

{{imports}}

{{classAnnotation}}
@Entity
@Table(name = "<?= $table; ?>")
<?php if (count(DB::getIdColumns($table))>1) { echo '@IdClass('.StringUtil::upper_AndFirst($table).'Id.class)'.PHP_EOL; } ?>
public class <?= $className ?>{

<? foreach ($cols as $col => $info) { ?>
<?= $info->getComment() ? "    /**".PHP_EOL ."     * ". $info->getComment() . PHP_EOL."     */".PHP_EOL : "" ?>
<?= $isExcelEntity ? JavaUtil::getExcelAnnotation($info) : '' ?>
<?= JavaUtil::getColumnAnnotation($info) ?>
    <?='@Column(name = "'.$info->getName().'")'.PHP_EOL?>
    private <?= JavaUtil::getJavaType($info) ?> <?= $col ?>;

<? } ?>
<? foreach ($cols as $col => $info) { ?>
    public <?= JavaUtil::getJavaType($info) ?> get<?= ucfirst($col); ?>(){
        return this.<? echo $col; ?>;
    }

    public void set<? echo ucfirst($col) . '(' . JavaUtil::getJavaType($info) . ' ' . $col; ?>){
        this.<? echo $col . '=' . $col; ?>;
    }

<? } ?>
    @Override
    public int hashCode() {
        return Objects.hash(<?= implode(',', array_keys($cols)) ?>);
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;
        <?= $className ?> that = (<?= $className ?>) o;
        return <?= implode(' && ', array_map(function ($col) {
    return 'Objects.equals(' . $col . ', that.' . $col . ')';
}, array_keys($cols))) ?>;
    }

}
<?= StringUtil::replacePlaceHolder('{{imports}}', JavaUtil::getImport()) //参数2的内容最后会替换行首{{imports}}的内容 ?>
<?= StringUtil::replacePlaceHolder('{{classAnnotation}}', JavaUtil::getClassAnnotationStr()) ?>
