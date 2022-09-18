<?php

use Bitrix\Main;
use Bitrix\Iblock;

Main\Loader::includeModule('iblock');

/**
 * @var Main\ORM\Query\Result $rsElement
 */
$rsElement = Iblock\Elements\ElementCatalogTable::getList([
	'select' => [
		'ID',
		'NAME',
		// Присоединить таблицу инфоблоков Iblock\IblockTable и выбрать SECTION_PAGE_URL
		'IBLOCK_SECTION_PAGE_URL' => 'IBLOCK.SECTION_PAGE_URL',
		// Присоединить таблицу разделов (Iblock\SectionTable)
		'IBLOCK_SECTION',
	],
]);

$arElements = [];
while ($obElement = $rsElement->fetchObject())
{
	$arElements[] = [
		'ID' => $obElement->getId(),
		'NAME' => $obElement->getName(),
		'SECTION_NAME' => $obElement->getIblockSection()->getName(),
		'SECTION_URL' => \CIBlock::ReplaceDetailUrl(
			$obElement->getIblock()->getSectionPageUrl(),
			$obElement->getIblockSection(), true, 'S'),
	];
}
