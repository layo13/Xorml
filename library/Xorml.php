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

			$tableName = $domEntity->getAttribute("tableName");

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

				$relation = $domAttribute->hasAttribute("relation") ? $domAttribute->getAttribute("relation") : NULL;
				$with = $domAttribute->hasAttribute("with") ? $domAttribute->getAttribute("with") : NULL;

				$column = new XormlColumn($columnName, $type, $size, $nullable, $isPrimaryKey, $strategy, $relation, $with);

				$table->addColumn($column);
			}

			echo $table->toQuery();
		}
	}

	public function generateMysqlManagers() {

		$domEntities = $this->doc->getElementsByTagName("entity");

		for ($i = 0; $i < $domEntities->length; $i++) {
			$domEntity = $domEntities->item($i);


			$name = $domEntity->getAttribute("name");
			$tableName = $domEntity->getAttribute("tableName");

			$content = "<?php\n\n";
			$content .= "namespace Library\Models;\n\n";
			$content .= "use \Library\Entities\\" . ucfirst($name) . ";\n\n";

			$content .= "class " . ucfirst($name) . "Manager_PDO extends " . ucfirst($name) . "Manager {;\n\n";

			$table = new XormlTable($tableName);
			$entity = new XormlEntity($name);

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

				$relation = $domAttribute->hasAttribute("relation") ? $domAttribute->getAttribute("relation") : NULL;
				$with = $domAttribute->hasAttribute("with") ? $domAttribute->getAttribute("with") : NULL;

				$column = new XormlColumn($columnName, $type, $size, $nullable, $isPrimaryKey, $strategy, $relation, $with);

				$attribute = new XormlAttribute($name);

				$table->addColumn($column);
				$entity->addAttribute($attribute);
			}

			$colums = $table->getColums();

			array_shift($colums);

			$content .= $this->getAdd($tableName, $colums);
			$content .= $this->getDelete($tableName);
			$content .= $this->getModify($tableName, $colums);
			$content .= $this->getGetList($tableName);
			$content .= $this->getGetUnique($tableName);

			echo($content);
			exit;
		}
	}

	private function getAdd($tableName, array $colums) {
		$content = "\tprotected function add(" . ucfirst($tableName) . " \$" . $tableName . ") {\n";
		$content .= "\t\t\$requete = \$this->dao->prepare('INSERT INTO " . $tableName . " ";

		for ($i = 0; $i < count($colums); $i++) {
			$colum = $colums[$i];
			if ($i == 0) {
				$content .= $colum->getColumnName();
			} else {
				$content .= ", {$colum->getColumnName()}";
			}
		}

		$content .= ") VALUES (";

		for ($i = 0; $i < count($colums); $i++) {
			$colum = $colums[$i];
			if ($i == 0) {
				$content .= $colum->getColumnName();
			} else {
				$content .= ", :{$colum->getColumnName()}";
			}
		}

		$content .= ")');\n\n";

		for ($i = 0; $i < count($colums); $i++) {
			$colum = $colums[$i];
			$content .= "\t\t\$requete->bindValue(':{$colum->getColumnName()}', \$" . $tableName . "->get" . ucfirst($colum->getColumnName()) . "());\n";
		}

		$content .= "\t\t\$requete->execute();\n";
		$content .= "\t}\n\n";
		return $content;
	}

	private function getModify($tableName, array $colums) {
		$content = "\tprotected function modify(" . ucfirst($tableName) . " \$" . $tableName . ") {\n";
		$content .= "\t\t\$requete = \$this->dao->prepare('UPDATE " . $tableName . " SET ";


		for ($i = 0; $i < count($colums); $i++) {
			$colum = $colums[$i];
			if ($i == 0) {
				$content .= "{$colum->getColumnName()} = :{$colum->getColumnName()}";
			} else {
				$content .= ", {$colum->getColumnName()} = :{$colum->getColumnName()}";
			}
		}

		$content .= " WHERE id = :id');\n\n";

		for ($i = 0; $i < count($colums); $i++) {
			$colum = $colums[$i];
			$content .= "\t\t\$requete->bindValue(':{$colum->getColumnName()}', \$" . $tableName . "->get" . ucfirst($colum->getColumnName()) . "());\n";
		}
		$content .= "\t\t\$requete->bindValue(':id', \$" . $tableName . "->getId(), \PDO::PARAM_INT);\n";
		$content .= "\t\t\$requete->execute();\n";
		$content .= "\t}\n\n";

		return $content;
	}

	private function getGetUnique($tableName) {
		$content = "\tpublic function getUnique(\$id) {\n";

		$content .= "\t\t\$requete = \$this->dao->prepare('SELECT * FROM $tableName WHERE id = :id');\n"; // Mettre chaque champ

		$content .= "\t\t\$requete->bindValue(':id', (int)\$id, \PDO::PARAM_INT);\n";

		$content .= "\t\t\$requete->execute();\n";

		$content .= "\t\t\$requete->execute();\n";

		$content .= "\t\t\$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\\" . ucfirst($tableName) . "');\n";

		$content .= "\t\tif (\$" . $tableName . " = \$requete->fetch()) {\n";

		$content .= "\t\t\treturn \$" . $tableName . ";\n";

		$content .= "\t\t}\n\n";

		$content .= "\t\treturn NULL;\n";

		$content .= "\t}\n\n";
		return $content;
	}

	private function getDelete($tableName) {
		$content = "\tpublic function delete(\$id) {\n";
		$content .= "\t\t\$this->dao->exec('DELETE FROM $tableName WHERE id = ' . (int) \$id);\n";
		$content .= "\t}\n\n";

		return $content;
	}

	private function getGetList($tableName) {
		$content = "\tpublic function getList(\$debut = -1, \$limite = -1) {\n";

		$content .= "\t\t\$sql = 'SELECT id, label, level, parent FROM " . $tableName . " ORDER BY id DESC';\n";

		$content .= "\t\tif (\$debut != -1 || \$limite != -1) {\n";
		$content .= "\t\t\t\$sql .= ' LIMIT ' . (int) \$limite . ' OFFSET ' . (int) \$debut;\n";
		$content .= "\t\t}\n";

		$content .= "\t\t\$requete = \$this->dao->query(\$sql);\n";
		$content .= "\t\t\$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\\" . ucfirst($tableName) . "');\n";
		$content .= "\t\t\$liste" . ucfirst($tableName) . " = \$requete->fetchAll();\n";
		$content .= "\t\t\$requete->closeCursor();\n";
		$content .= "\t\treturn \$liste" . ucfirst($tableName) . ";\n";

		$content .= "\t}\n\n";

		return $content;
	}

	private function getAbstractManager($name) {
		$content = "<?php\n";

		$content .= "namespace Library\Models;\n\n";

		$content .= "use Library\Entities\\" . ucfirst($name) . ";\n\n";

		$content .= "abstract class " . ucfirst($name) . "Manager extends \Library\Manager {\n\n";

		$content .= "\tabstract protected function add(" . ucfirst($name) . " \$" . $name . ");\n\n";

		$content .= "\tabstract protected function modify(" . ucfirst($name) . " \$" . $name . ");\n\n";

		$content .= "\tabstract public function delete(\$id);\n\n";

		$content .= "\tpublic function save(" . ucfirst($name) . " \$" . $name . ") {\n\n";
		$content .= "\t\t\$" . $name . "->isNew() ? \$this->add(\$" . $name . ") : \$this->modify(\$" . $name . ");\n\n"; // isNew a rechechir
		$content .= "\t}\n\n";

		$content .= "\tabstract public function getList(\$debut = -1, \$limite = -1);\n\n";

		$content .= "\tabstract public function getUnique(\$id);\n\n";

		$content .= "\tabstract public function count();\n\n";
		$content .= "}\n\n";

		return $content;
	}

}
