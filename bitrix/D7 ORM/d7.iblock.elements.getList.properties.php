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
		// Свойство типа Строка
		'ARTNUMBER_' => 'ARTNUMBER', // Значение строки
		// Свойство типа Список
		'NEWPRODUCT_' => 'NEWPRODUCT.ITEM', // ID элемента списка. .ITEM присоединяет элемент из таблицы (со значением)
		// Множественное свойство типа Файл
		'MORE_PHOTO.FILE',
	],
]);

$arElements = [];
while ($obElement = $rsElement->fetchObject())
{
	$arMorePhoto = [];
	foreach ($obElement->getMorePhoto()->getAll() as $fileProperty) {
		$arMorePhoto[] = getPropertyFileSrcUrl($fileProperty->getFile());
	}

	$arElements[] = [
		'ID' => $obElement->getId(),
		'NAME' => $obElement->getName(),
		'ARTNUMBER' => $obElement->getArtnumber()->getValue(),
		'NEWPRODUCT' => $obElement->getNewproduct() ? 1 === $obElement->getNewproduct()->getValue() ? 'Y' : 'N' : 'N',
		'MORE_PHOTO' => $arMorePhoto,
	];
}

function getPropertyFileSrcUrl($arPropertyFile) {
	return '/upload/' . $arPropertyFile->getSubdir() . '/' . $arPropertyFile->getFileName();
}
