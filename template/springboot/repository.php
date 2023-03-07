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

$className = StringUtil::upper_AndFirst($table);
util::setSavePath($savePath, $className . '.java');
?>
package <?= $package ?>;

import <?= $entityPackage . '.' . $className ?>;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;

public interface IotThingFunTblRepository extends JpaRepository<<?= $className ?>, Integer>, JpaSpecificationExecutor<<?= $className ?>> {
}
