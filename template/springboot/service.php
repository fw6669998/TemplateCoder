<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\StringUtil;
use src\JavaUtil;
use src\util;

$table = util::param('table');
$basePackage = util::param('basePackage');
$savePath = util::param('baseSavePath') . '\\' . JavaUtil::$packageService;
$package = $basePackage . '.' . JavaUtil::$packageService;

$repositoryPackage = $basePackage . '.' . JavaUtil::$packageRepository;
$entityClass = StringUtil::upper_AndFirst($table);
$serviceClass = (str_ends_with($entityClass, 'Tbl') ? str_replace('Tbl', '', $entityClass) : $entityClass).'Service';
$repositoryClass = $entityClass.'Repository';
$repositoryVar = lcfirst($repositoryClass);
util::setSavePath($savePath, $serviceClass.'.java');

?>
package <?= $package ?>;

import <?= $repositoryPackage.'.'.$repositoryClass ?>;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

@Component
public class <?= $serviceClass ?> {

	@Autowired
	public <?=$repositoryClass?> <?=$repositoryVar?>;

}