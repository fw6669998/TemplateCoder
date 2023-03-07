<?php

namespace src;

use Illuminate\Database\Capsule\Manager as DBManager;

class DB
{
    public static $conn;
    /**
     * @var \Illuminate\Database\Schema\Builder $schema
     */
    public static $schema;

    public static $config = [];

    public static function getDBConfig()
    {
        if (self::$config) {
            return self::$config;
        }
        $configFile = __DIR__ . '\..\config\database.json';
        $content = file_get_contents($configFile);
        $config = json_decode($content, true);
        self::$config = $config;
        return $config;
    }

    public static function connect()
    {
        if (self::$conn) {
            return self::$conn;
        }
        $capsule = new DBManager;
        $config = self::getDBConfig();

        $db = $_GET['__db'];
        $db = $db && isset($config[$db]) ? $db : array_keys($config)[0];

        $capsule->addConnection($config[$db]);
        self::$conn = $capsule->getConnection();
        self::$schema = $capsule->getDatabaseManager()->getSchemaBuilder();
        return self::$conn;
    }

    public static function getTables()
    {
        self::connect();
        $tables = self::$schema->getAllTables();
        $tables2 = [];
        foreach ($tables as $key => $value) {
            foreach ($value as $k => $v) {
                $tables2[] = $v;
                break;
            }
        }
        return $tables2;
    }

    public static function getColumns($table)
    {
        $conn = self::connect();
        $columns = $conn->getSchemaBuilder()->getColumnListing($table);
        return $columns;
    }

    public static function getColumnTypes($table)
    {
        self::connect();
        $res = self::$schema->getColumnListing($table);
        $res2 = [];
        foreach ($res as $key => $value) {
            $res2[$value] = self::$schema->getColumnType($table, $value);
        }
        return $res2;
    }

    /**
     * @param $table
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public static function getColumnInfos($table)
    {
        $conn = self::connect();

        $schema = $conn->getDoctrineSchemaManager();
        $tableDetail = $schema->listTableDetails($table);
        $key = $tableDetail->getPrimaryKey();
        $keyName = '';
        if ($key) {
            $keyName = $key->getColumns()[0];
        }
        $columns = $tableDetail->getColumns();
        $columns2 = [];
        foreach ($columns as $column) {
            if ($column->getName() == $keyName) {
                $column->isPrimary = true;
            }
            $columns2[$column->getName()] = $column;
        }
        return $columns2;
    }
}