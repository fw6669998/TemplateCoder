<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\StringUtil;
use src\JavaUtil;
use src\util;

$table = util::param('table');
$basePackage = util::param('basePackage');
$savePath = util::param('baseSavePath'). '\\' . JavaUtil::$packageRepository;
$package = $basePackage . '.' . JavaUtil::$packageRepository;

$entityPackage = $basePackage . '.' . JavaUtil::$packageEntity;
$tableName = StringUtil::upper_AndFirst($table);
util::setSavePath($savePath, $tableName . 'Repository.java');
?>
package <?= $package ?>;

import <?= $entityPackage . '.' . $tableName ?>;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;

public interface <?= $tableName ?>Repository extends JpaRepository<<?= $tableName ?>, <?= JavaUtil::getPrimaryKeyType($table) ?>>, JpaSpecificationExecutor<<?= $tableName ?>> {
}
