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
	
	public function getName() {
		return $this->name;
	}

	public function getColums() {
		return $this->colums;
	}

	public function getEngine() {
		return $this->engine;
	}

	public function getDefaultCharset() {
		return $this->defaultCharset;
	}

	public function getCollate() {
		return $this->collate;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setColums($colums) {
		$this->colums = $colums;
	}

	public function setEngine($engine) {
		$this->engine = $engine;
	}

	public function setDefaultCharset($defaultCharset) {
		$this->defaultCharset = $defaultCharset;
	}

	public function setCollate($collate) {
		$this->collate = $collate;
	}

	
	public function addColumn(XormlColumn $column) {
		$this->colums[] = $column;
	}
	
	public function toQuery() {
		$content = "CREATE TABLE `". $this->name ."` (".chr(10);

		foreach ($this->colums as $column) {
			$content .= $column->toQuery();
		}
		$content .= "PRIMARY KEY (`id`)";
		$content .= ") ENGINE=".$this->engine." DEFAULT CHARSET=".$this->defaultCharset." COLLATE=".$this->collate.";";
		return $content;
	}
}
