<?php

namespace library;

class XormlColumn {

	private $columnName;
	private $type;
	private $size;
	private $nullable;
	
	private $isPrimaryKey;
	private $strategy;
	private $relation;
	private $with;
	private $collate;

	public function __construct($columnName, $type, $size, $nullable = FALSE, $isPrimaryKey = FALSE, $strategy = NULL, $relation = NULL, $with = NULL, $collate = "utf8_unicode_ci") {
		$this->columnName = $columnName;
		$this->type = $type;

		if ($type == "string" || $type == "text") {
			$this->collate = $collate;
		} else {
			$this->collate = NULL;
		}

		$this->size = $size;
		$this->nullable = $nullable;

		$this->isPrimaryKey = $isPrimaryKey;
		$this->strategy = $strategy;
		
		$this->relation = $relation;
		$this->with = $with;
	}
	
	public function getColumnName() {
		return $this->columnName;
	}

	public function getType() {
		return $this->type;
	}

	public function getSize() {
		return $this->size;
	}

	public function getNullable() {
		return $this->nullable;
	}

	public function getIsPrimaryKey() {
		return $this->isPrimaryKey;
	}

	public function getStrategy() {
		return $this->strategy;
	}

	public function getRelation() {
		return $this->relation;
	}

	public function getWith() {
		return $this->with;
	}

	public function getCollate() {
		return $this->collate;
	}

	public function setColumnName($columnName) {
		$this->columnName = $columnName;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function setSize($size) {
		$this->size = $size;
	}

	public function setNullable($nullable) {
		$this->nullable = $nullable;
	}

	public function setIsPrimaryKey($isPrimaryKey) {
		$this->isPrimaryKey = $isPrimaryKey;
	}

	public function setStrategy($strategy) {
		$this->strategy = $strategy;
	}

	public function setRelation($relation) {
		$this->relation = $relation;
	}

	public function setWith($with) {
		$this->with = $with;
	}

	public function setCollate($collate) {
		$this->collate = $collate;
	}

	
	public function toQuery() {
		$content = chr(9) . "`" . $this->columnName . "` ";

		if ($this->type == "int" || $this->relation == "ManyToOne") {
			$content .= "int(11) ";
		} else if ($this->type == "float") {
			$content .= "float ";
		} else if ($this->type == "text") {
			$content .= "text COLLATE " . $this->collate . " ";
		} else if ($this->type == "datetime") {
			$content .= "datetime ";
		} else if ($this->type == "string") {
			$content .= "varchar(" . $this->size . ") COLLATE " . $this->collate . " ";
		}



		if ($this->nullable == FALSE) {
			$content .= "NOT NULL," . chr(10);
		} else {
			$content .= "NULL," . chr(10);
		}

		return $content;
	}

}
