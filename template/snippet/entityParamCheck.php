<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\StringUtil;
use src\DB;
use src\JavaUtil;
use src\util;

$table = util::param('table');

$entityClass = StringUtil::upper_AndFirst($table);
$entityName = JavaUtil::clearEntitySuffix($entityClass);
$entityVar = lcfirst($entityClass);
$cols = DB::getColumnInfos($table);

//获取最后一个元素的key
?>

public String check<?=$entityName?>Param(<?=$entityClass?> <?=$entityVar?>){
<?php foreach ($cols as $col => $info) { ?>
<?php if ($info->getNotnull()) {
    $comment = explode(':',$info->getComment())[0];
    if (!$comment)$comment=$col;
    ?>
<?php if (JavaUtil::getJavaType($info)=='String') {?>
	if (StringUtils.isBlank(<?=$entityVar?>.get<?=ucfirst($col)?>())){
		return "<?=$comment?>不能为空";
	}
<?php }else{ ?>
	if (<?=$entityVar?>.get<?=ucfirst($col)?>()==null){
		return "<?=$comment?>不能为空";
	}
<?php }}} ?>
	return null;
}