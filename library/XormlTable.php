<?php

namespace library;

use library\XormlColumn;

class XormlTable {
	private $name;
	private $colums;
	private $engine;
	private $defaultCharset;
	private $collate;
	
	function __construct($name, $engine = "InnoDB", $defaultCharset = "utf8", $collate = "utf8_unicode_ci") {
		$this->name = $name;
		$this->colums = array();
		
		$this->engine = $engine;
		$this->defaultCharset = $defaultCharset;
		$this->collate = $collate;
	}

	public function addColumn(XormlColumn $column) {
		$this->colums[] = $column;
	}
	
	public function toQuery() {
		$content = "CREATE TABLE `ad` (".chr(10);

		foreach ($this->colums as $column) {
			$content .= $column->toQuery();
		}
		$content .= ") ENGINE=".$this->engine." DEFAULT CHARSET=".$this->defaultCharset." COLLATE=".$this->collate.";";
		return $content;
	}
}
