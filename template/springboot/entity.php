<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\DB;
use src\JavaUtil;
use src\StringUtil;
use src\util;

//获取表名
$table = $_GET['table'];
//定义和获取模板参数
$basePackage = util::param('basePackage');
$savePath = util::param('baseSavePath') . '\\' . JavaUtil::$packageEntity;
$package = $basePackage . '.' . JavaUtil::$packageEntity;
$isExcelEntity = util::param('entityExcel');

$_cols = DB::getColumnInfos($table);
$cols = StringUtil::colsUpper_($_cols);
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
public class <?= $className ?>{

<? foreach ($cols as $col => $info) { ?>
<?= $info->getComment() ? "\t//" . $info->getComment() . PHP_EOL : '' ?>
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
