<?php

namespace src;

use Doctrine\DBAL\Schema\Column;

class JavaUtil
{
    public static $imports = [];

    public static $package = '';

    public static $classAnnotations = [];


    public static function addImport($type)
    {
        self::$imports[$type] = $type;
    }

    public static function addClassAnnotation($annotation)
    {
        self::$classAnnotations[$annotation] = $annotation;
    }

    /**
     * 获取与该列类型相关的注解
     * @param Column $colInfo
     * @return void
     */
    public static function getColumnAnnotation($colInfo)
    {
        $res = '';
        $haveAutoDate = false;
        if ($colInfo->getType()->getName() == 'datetime') {
            if ($colInfo->getName() == 'create_time') {
                $res .= "\t@CreatedDate" . PHP_EOL . "\t" . '@DateTimeFormat(pattern = "yyyy-MM-dd HH:mm:ss")' . PHP_EOL;
                self::addImport('org.springframework.data.annotation.CreatedDate');
                $haveAutoDate = true;
            } else if ($colInfo->getName() == 'update_time') {
                $res .= "\t@LastModifiedDate" . PHP_EOL . "\t" . '@DateTimeFormat(pattern = "yyyy-MM-dd HH:mm:ss")' . PHP_EOL;
                self::addImport('org.springframework.data.annotation.LastModifiedDate');
                $haveAutoDate = true;
            }
            if ($haveAutoDate) {
                self::addClassAnnotation('@EntityListeners(AuditingEntityListener.class)');
                self::addImport('org.springframework.data.jpa.domain.support.AuditingEntityListener');
                self::addImport('org.springframework.format.annotation.DateTimeFormat');
            }
        }
        if ($colInfo->isPrimary) {
            $res .= "\t@Id" . PHP_EOL;
        }

        return $res;
    }

    /**
     * @param Column $colInfo
     * @param $addImport
     * @return string
     */
    public static function getJavaType($colInfo, $addImport = true)
    {
        $dbType = $colInfo->getType()->getName();
        $dbType = strtolower($dbType);
        $javaType = "String";
        switch ($dbType) {
            case 'int':
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'bigint':
                $javaType = 'Integer';
                break;
            case 'float':
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
//            case 'char':
//            case 'varchar':
//            case 'tinytext':
//            case 'text':
//            case 'mediumtext':
//            case 'longtext':
//            case 'enum':
//            case 'set':
//                $javaType = 'String';
//                break;
        }
        if ($addImport) {
            if (in_array($javaType, ['Date'])) {
                self::addImport($javaType);
            }
        }
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
}