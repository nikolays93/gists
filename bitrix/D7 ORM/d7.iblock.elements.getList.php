<?php

use Bitrix\Main;
use Bitrix\Iblock;

Main\Loader::includeModule('iblock');

/**
 * @var array $arElements
 */
$arElements = Iblock\ElementTable::getList([
	'select' => ['*'],
	// Отфильтровать по таблице инфоблоков Iblock\IblockTable где CODE === 'news'
	'filter' => ['=IBLOCK.CODE' => 'news'],
	'order' => ['DATE_CREATE' => 'DESC', 'NAME' => 'ASC'],
	'limit' => 1, // Только один элемент
	'offset' => 2, // Начиная с третьего
	"cache" => ["ttl" => 3600, 'cache_joins' => true],
])->fetchAll();
