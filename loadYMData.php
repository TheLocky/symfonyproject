<?php

$catalog = file_get_contents('https://market.yandex.ru/?tab=catalog');
$catalog = @DOMDocument::loadHTML($catalog);

$finder = new DomXPath($catalog);
$classname="catalog-simple__item";
$topCats = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

class Cat {
	public $name;
	public $subCats;

	public function __construct()
	{
		$this->subCats = array();
	}
}

class Item {
	public $name;
	public $img;
	public $cost;
}

$cats = array();
$id = 1;

for ($i=1; $i < 4; $i++) { 
	$topCat = $topCats[$i];
	$href = $topCat->childNodes[0]->getAttribute('href');
	$name = $topCat->childNodes[0]->textContent;
	echo $name . ' ' . $href;
	foreach ($topCat->childNodes[1]->childNodes as $cat) {
		var_dump($cat);
	}
}

exit();