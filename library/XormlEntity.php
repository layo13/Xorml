<?php

namespace library;

use library\XormlAttribute;

class XormlEntity {
	private $name;
	private $attributes;
	
	function __construct($name) {
		$this->name = $name;
		$this->attributes = array();
	}
	
	public function getName() {
		return $this->name;
	}

	public function getAttributes() {
		return $this->attributes;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setAttributes($attributes) {
		$this->attributes = $attributes;
	}
	
	public function addAttribute(XormlAttribute $attribute) {
		$this->attributes[] = $attribute;
	}
	
	public function toFile() {
		$content = "<?php";
		$content .= str_repeat(chr(10), 2);
		$content .= "namespace Library\Entity;";
		$content .= str_repeat(chr(10), 2);
		$content .= "class " . ucfirst($this->name) . " {".chr(10);
		
		foreach ($this->attributes as $attribute) {
			$content .= chr(9)."private \$".$attribute->getName().";".chr(10);
		}
		$content .= chr(10);
		foreach ($this->attributes as $attribute) {
			$content .= chr(9)."public function get".ucfirst($attribute->getName())."() {".chr(10);
			$content .= str_repeat(chr(9), 2)."return \$this->".$attribute->getName().";".chr(10);
			$content .= chr(9)."}".chr(10);
			
			$content .= chr(9)."public function set".ucfirst($attribute->getName())."(\$".$attribute->getName().") {".chr(10);
			$content .= str_repeat(chr(9), 2)."\$this->".$attribute->getName()." = \$".$attribute->getName().";".chr(10);
			$content .= chr(9)."}".chr(10);
		}
		$content .= "}";
		return $content;
	}
}
