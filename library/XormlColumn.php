<?php

namespace library;

class XormlColumn {

	private $columnName;
	private $type;
	private $size;
	private $nullable;
	private $collate;

	public function __construct($columnName, $type, $size, $nullable = FALSE, $isPrimaryKey = FALSE, $strategy = NULL, $collate = "utf8_unicode_ci") {
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
	}

	public function toQuery() {
		$content = chr(9) . "`" . $this->columnName . "` ";

		if ($this->type == "int") {
			$content .= "int(11) ";
		} else if ($this->type == "float") {
			$content .= "float ";
		} else if ($this->type == "text") {
			$content .= "text " . $this->collate . " ";
		} else if ($this->type == "datetime") {
			$content .= "datetime ";
		} else if ($this->type == "string") {
			$content .= "varchar(" . $this->size . ") " . $this->collate . " ";
		}

		if ($this->nullable == FALSE) {
			$content .= "NOT NULL," . chr(10);
		} else {
			$content .= "NULL," . chr(10);
		}

		return $content;
	}

}
