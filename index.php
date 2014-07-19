<?php

header("Content-type: text/plain; Charset=UTF-8");

require dirname(__FILE__)."/./XormlLoader.php";

XormlAutoloader::register();

use library\Xorml;

$doc = new DOMDocument("1.0", "UTF-8");
$doc->load("db.xml");

$xorml = new \library\Xorml($doc);

$xorml->generateTables();
//$xorml->generateEntities();