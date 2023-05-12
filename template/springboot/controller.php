<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\StringUtil;
use src\util;
use src\JavaUtil;

$table = util::param('table');

$basePackage = util::param('basePackage');
$savePath = util::param('baseSavePath') . '\\' . JavaUtil::$packageController;
$package = $basePackage . '.' . JavaUtil::$packageController;

$entityClass = StringUtil::upper_AndFirst($table);
$serviceClass = JavaUtil::clearEntitySuffix($entityClass) . 'Service';
$controllerClass = JavaUtil::clearEntitySuffix($entityClass) . 'Controller';
$repositoryVar = lcfirst($serviceClass);

util::setSavePath($savePath, $controllerClass . '.java');
?>
package <?= $package ?>;

import <?= $basePackage . '.service.' . $serviceClass ?>;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.ResponseBody;

import javax.servlet.http.HttpServletRequest;
import java.util.Map;

@Component
@RequestMapping("/<?=JavaUtil::clearEntitySuffix($entityClass)?>")
public class <?= $controllerClass ?> extends BaseController{

	@Autowired
	private <?= $serviceClass ?> <?= $repositoryVar ?>;

	@RequestMapping("/create")
	@ResponseBody
	public Map<String, Object> list(HttpServletRequest request, <?=$entityClass?> <?=lcfirst($entityClass)?>) {

		return result(null);
	}

}
