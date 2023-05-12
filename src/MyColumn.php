<?php

namespace src;

use Doctrine\DBAL\Schema\Column;

class MyColumn
{
	private $column;

	public $isPrimaryKey = false;

	public $isUnique = false;

	public function __construct(Column $column)
	{
//		$arr=$column->toArray();
//		$this->setOptions($arr);
//		parent::__construct($column->getName(), $column->getType(), $column->toArray());
		$this->column = $column;
	}

	// 基本方法从$column对象中调用
//	public function __call($name, $arguments)
//	{
//		$this->column->$$name($arguments);
//		return call_user_func_array([$this->column, $name], $arguments);
//	}

	public function getName()
	{
		return $this->column->getName();
	}

	public function getVarName()
	{
		return StringUtil::upper_($this->column->getName());
	}

	public function isPrimaryKey()
	{
		return $this->isPrimaryKey;
	}

	public function isReserved()
	{
		return $this->isReservedByName() || $this->isReservedByFlag() || $this->isPrimaryKey();
	}

	public function isReservedByName()
	{
		$reservedFields = ['id', 'create_time', 'updateTime', 'createBy', 'updateBy', 'creator_id', 'is_delete'];
		return in_array($this->getName(), $reservedFields);
	}

	public function isReservedByFlag()
	{
		return strpos($this->column->getComment(), '{r}');
	}

	public function isUnique()
	{
//		return $this->column->
//		return strpos($this->column->getComment(), '{u}');
	}

	public function getComment()
	{
		$comment = $this->column->getComment();
		//通过正则清理{x}这种格式的注释
		$comment = preg_replace('/\{.*?\}/', '', $comment);
		return $comment;
	}

	public function getShortComment()
	{
		return explode(":", $this->getComment())[0];
	}

	public function getNotnull()
	{
		return $this->column->getNotnull();
	}

	public function getType()
	{
		return $this->column->getType()->getName();
	}

	/**
	 * @return bool
	 */
	public function isAutoincrement()
	{
		return $this->column->getAutoincrement();
	}
}