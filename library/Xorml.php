<?php

namespace library;

class Xorml {

	private $doc;

	function __construct(\DOMDocument $doc) {
		$this->doc = $doc;
	}

	public function generateDatabase() {
		
	}

	public function generateEntities() {
		$destination = "/./Entity/";

		$domEntities = $this->doc->getElementsByTagName("entity");

		for ($i = 0; $i < $domEntities->length; $i++) {
			$domEntity = $domEntities->item($i);

			$entityName = $domEntity->getAttribute("name");

			$entity = new XormlEntity($entityName);

			$domAttributes = $domEntity->getElementsByTagName("attribute");

			for ($j = 0; $j < $domAttributes->length; $j++) {
				/* @var $domAttribute \DOMElement */
				$domAttribute = $domAttributes->item($j);

				$attributeName = $domAttribute->nodeValue;

				$attribute = new XormlAttribute($attributeName);

				$entity->addAttribute($attribute);
			}
		}
	}

	public function generateTables() {

		$domEntities = $this->doc->getElementsByTagName("entity");

		for ($i = 0; $i < $domEntities->length; $i++) {
			$domEntity = $domEntities->item($i);

			$tableName = $domEntity->getAttribute("name");

			$table = new XormlTable($tableName);

			$domAttributes = $domEntity->getElementsByTagName("attribute");

			for ($j = 0; $j < $domAttributes->length; $j++) {
				/* @var $domAttribute \DOMElement */
				$domAttribute = $domAttributes->item($j);

				$columnName = $domAttribute->getAttribute("columnName");
				
				if ($domAttribute->hasAttribute("type")) {
					$type = $domAttribute->getAttribute("type");
				} else {
					$type = NULL;
				}
				
				$size = $domAttribute->hasAttribute("size") ? $domAttribute->getAttribute("size") : NULL;
				$nullable = $domAttribute->hasAttribute("nullable") ? $domAttribute->getAttribute("nullable") : FALSE;
				$isPrimaryKey = $domAttribute->hasAttribute("isPrimaryKey") ? $domAttribute->getAttribute("isPrimaryKey") : NULL;
				$strategy = $domAttribute->hasAttribute("strategy") ? $domAttribute->getAttribute("strategy") : NULL;
				

				$column = new XormlColumn($columnName, $type, $size, $nullable, $isPrimaryKey, $strategy);

				$table->addColumn($column);
			}
			echo $table->toQuery();exit;
		}
	}
}
