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

	private static $colCache = [];

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

		$db = util::param('__db');
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

	/**
	 * @param $table
	 * @return MyColumn[]
	 */
	public static function getColumnInfos($table)
	{
		if (isset(self::$colCache[$table])) {
			return self::$colCache[$table];
		}

		$conn = self::connect();

		$schema = $conn->getDoctrineSchemaManager();
		$tableDetail = $schema->listTableDetails($table);
		$indexs = $tableDetail->getIndexes();
		$columns = $tableDetail->getColumns();
		$columns2 = [];
		foreach ($columns as $column) {
			$myColumn = new MyColumn($column);
			foreach ($indexs as $index) {
				if ($index->isPrimary()) {
					foreach ($index->getColumns() as $indexColumnName) {
						if ($indexColumnName == $myColumn->getName()) {
							$myColumn->isPrimaryKey = true;
						}
					}
				} else if ($index->isUnique()) {
					foreach ($index->getColumns() as $indexColumnName) {
						if ($indexColumnName == $myColumn->getName()) {
							$myColumn->isUnique = true;
						}
					}
				}
			}
			$columns2[$myColumn->getVarName()] = $myColumn;
		}
		self::$colCache[$table] = $columns2;
		return $columns2;
	}

	/**
	 * @param $table
	 * @return MyColumn[]
	 */
	public static function getIdColumns($table)
	{
		$columns = self::getColumnInfos($table);
		$ids = [];
		foreach ($columns as $column) {
			if ($column->isPrimaryKey()) {
				$ids[] = $column;
			}
		}
		return $ids;
	}
}