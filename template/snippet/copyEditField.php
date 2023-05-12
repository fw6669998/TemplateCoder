<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\StringUtil;
use src\DB;
use src\JavaUtil;
use src\util;

$table = util::param('table');
$paramsStr = util::param('apiPostParam');
$params = json_decode($paramsStr, true);

$entityClass = StringUtil::upper_AndFirst($table);
$entityName = JavaUtil::clearEntitySuffix($entityClass);
$entityVar = lcfirst($entityClass);
$cols = DB::getColumnInfos($table);

?>
public void copyEditProperties(<?=$entityClass?> paramObj, <?=$entityClass?> dbObj) {
<?php foreach ($params as $key=>$val){
    if (is_array($val)) $key=$val['key'];
    if (!isset($cols[$key])) continue;
    if ($cols[$key]->isReserved()) continue;
    ?>
    dbObj.set<?=ucfirst($key)?>(paramObj.get<?=ucfirst($key)?>());
<?php }?>
}
