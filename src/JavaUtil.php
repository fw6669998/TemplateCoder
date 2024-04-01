<?php

namespace src;

class JavaUtil
{
	public static $packageEntity = 'entity';
	public static $packageController = 'controller';
	public static $packageService = 'service';
	public static $packageRepository = 'repository';

	public static $imports = [];

	public static $package = '';

	public static $classAnnotations = [];

	public static $javaTypeFullNames = [
		"Date" => "java.util.Date",
	];


	public static function addImport($type)
	{
		//以java.lang.开头的不需要导入
		if (strpos($type, 'java.lang.') === 0) {
			return;
		}
		//基本类型不需要导入
		if (in_array($type, ["int", "long", "double", "float", "boolean", "short", "byte", "char", "byte[]",
			"Integer", "Long", "Double", "Float", "Boolean", "Short", "Byte", "Character", "String"])) {
			return;
		}
		//如果是全限定名，直接导入
		if (strpos($type, '.') !== false) {
			self::$imports[$type] = $type;
		} else {
			//如果是简单类型，查找是否有全限定名
			if (isset(self::$javaTypeFullNames[$type])) {
				self::$imports[self::$javaTypeFullNames[$type]] = self::$javaTypeFullNames[$type];
			}
		}
	}

	public static function addClassAnnotation($annotation)
	{
		self::$classAnnotations[$annotation] = $annotation;
	}

	/**
	 * 获取与该列类型相关的注解
	 * @param MyColumn $colInfo
	 * @return void
	 */
	public static function getColumnAnnotation($colInfo)
	{
		$res = '';
		$haveAutoDate = false;
		//添加主键注解
		if ($colInfo->isPrimaryKey()) {
			$res .= "\t@Id" . PHP_EOL;
		}
		//添加时间注解
		if ($colInfo->getType() == 'datetime') {
			$res .= "\t@DateTimeFormat(pattern = \"yyyy-MM-dd HH:mm:ss\")" . PHP_EOL;
			self::addImport('org.springframework.format.annotation.DateTimeFormat');
			if ($colInfo->getName() == 'create_time') {
				$res .= "\t@CreatedDate" . PHP_EOL;
				self::addImport('org.springframework.data.annotation.CreatedDate');
				$haveAutoDate = true;
			} else if ($colInfo->getName() == 'update_time') {
				$res .= "\t@LastModifiedDate" . PHP_EOL;
				self::addImport('org.springframework.data.annotation.LastModifiedDate');
				$haveAutoDate = true;
			}
			if ($haveAutoDate) {
				self::addClassAnnotation('@EntityListeners(AuditingEntityListener.class)');
				self::addImport('org.springframework.data.jpa.domain.support.AuditingEntityListener');
			}
		}
		//添加自增注解
		if ($colInfo->isAutoincrement()) {
			$res .= "\t@GeneratedValue(strategy = GenerationType.IDENTITY)" . PHP_EOL;
		}
		//添加参数校验注解
		if ($colInfo->getNotnull() && !$colInfo->isAutoincrement() && !$colInfo->isPrimaryKey()) {
			$res .= "\t@NotNull(message = \"".$colInfo->getShortComment()."不能为空\")" . PHP_EOL;
			self::addImport('javax.validation.constraints.NotNull');
		}

		return $res;
	}

	/**
	 * @param MyColumn $colInfo
	 * @return string
	 */
	public static function getJavaType($colInfo)
	{
		$dbType = $colInfo->getType();
		$dbType = strtolower($dbType);
		$javaType = "";
		switch ($dbType) {
			case 'int':
			case 'integer':
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'bigint':
			case 'boolean':
				$javaType = 'Integer';
				break;
			case 'float':
				$javaType = 'Float';
				break;
			case 'double':
			case 'decimal':
				$javaType = 'Double';
				break;
			case 'date':
			case 'time':
			case 'year':
			case 'datetime':
			case 'timestamp':
				$javaType = 'Date';
				break;
			case 'blob':
			case 'tinyblob':
			case 'mediumblob':
			case 'longblob':
				$javaType = 'byte[]';
				break;
			case 'char':
			case 'varchar':
			case 'tinytext':
			case 'text':
			case 'mediumtext':
			case 'longtext':
			case 'enum':
			case 'set':
			case 'string':
				$javaType = 'String';
				break;
		}
		self::addImport($javaType);
		return $javaType;
	}

	public static function getImport()
	{
		$res = '';
		foreach (self::$imports as $import) {
			$res .= "import " . $import . ";" . PHP_EOL;
		}
		if ($res)
			$res = substr($res, 0, -strlen(PHP_EOL));
		return $res;
	}

	public static function getClassAnnotationStr()
	{
		$res = '';
		if (!empty(JavaUtil::$classAnnotations)) {
			foreach (JavaUtil::$classAnnotations as $annotation) {
				$res .= $annotation . PHP_EOL;
			}
		}
		if ($res)
			$res = substr($res, 0, -strlen(PHP_EOL));
		return $res;
	}

	public static function getRelativeSavePath($package, $className)
	{
		return implode('\\', explode('.', $package)) . '\\' . $className . '.java';
	}

	/**
	 * @param $table
	 * 获取表的主键类型
	 */
	public static function getPrimaryKeyType($table)
	{
		$cols = DB::getColumnInfos($table);
		foreach ($cols as $col) {
			if ($col->isPrimaryKey()) {
				return self::getJavaType($col);
			}
		}
		return 'Integer';
	}


	/**
	 * @param MyColumn $colInfo
	 * @return string
	 */
	public static function getExcelAnnotation($colInfo)
	{
		self::addImport('com.alibaba.excel.annotation.ExcelProperty');
		return "\t" . '@ExcelProperty(value = "' . $colInfo->getComment() . '")' . PHP_EOL;
	}

	public static function clearEntitySuffix($entityClass)
	{
		if (str_ends_with($entityClass, 'Tbl')) {
			return str_replace('Tbl', '', $entityClass);
		}
		return $entityClass;
	}

	public static function isReservedField($fieldName)
	{
		$reservedFields = ['id', 'createTime', 'updateTime', 'createBy', 'updateBy', 'creator_id', 'is_delete'];
		return in_array($fieldName, $reservedFields);
	}
}