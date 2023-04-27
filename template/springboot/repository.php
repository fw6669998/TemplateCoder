<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\StringUtil;
use src\JavaUtil;
use src\util;

$table = $_GET['table'];
$package = util::param('package', 'com.iuv.repository');
$entityPackage = util::param('entityPackage', 'com.iuv.domain');
$savePath = util::param('savePath', '');
util::returnParamDefine(__FILE__);

$tableName = StringUtil::upper_AndFirst($table);
util::setSavePath($savePath, $tableName . 'Repository.java');
?>
package <?= $package ?>;

import <?= $entityPackage . '.' . $tableName ?>;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;

public interface <?=$tableName?>Repository extends JpaRepository<<?= $tableName ?>, Integer>, JpaSpecificationExecutor<<?= $tableName ?>> {
}
