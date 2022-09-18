<?php

/** @var int $iblockId ИД Инфоблока каталога */

use Bitrix\Main;
use Bitrix\Iblock;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Query\Join;

Main\Loader::includeModule('iblock');
Main\Loader::includeModule('highloadblock');

function compileHighLoadEntities($entity, $hlFields)
{
    $elementTableFields = array_flip($hlFields);
    // Get High loads by name.
    $hlEntities = HL\HighloadBlockTable::getList(['filter' => ['NAME' => $hlFields]])->fetchAll();

    foreach ($hlEntities as $hlEntity) {
        // Create Highload entity.
        $referenceEntity = HL\HighloadBlockTable::compileEntity($hlEntity);
        // Get reference field name
        $elementTableField = $elementTableFields[$hlEntity['NAME']];
        // Extract ref from ElementList entity
        $refValueEntity = $entity->getField($elementTableField)->getRefEntity();
        // Add HIGHLOAD link
        $refValueEntity->addField(new ReferenceField(
            'HIGHLOAD',
            $referenceEntity,
            Join::on('this.VALUE', 'ref.UF_XML_ID')
        ));
    }
}

// API Code обязателен, иначе вернет false
$entityClassName = Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
// Добавит LEFT JOIN связь с таблицей хайлоад блока и добавит значение свойства в BRAND_REF.HIGHLOAD
compileHighLoadEntities($entityClassName::getEntity(), [
    // Property name => Highload block name
    'BRAND_REF' => 'BrandReference',
]);

/**
 * @var Main\ORM\Query\Result $rsElement
 */
$rsElement = $entityClassName::getList([
    'select' => [
        'ID',
        'NAME',
        'BRAND_REF.HIGHLOAD',
    ],
    'filter' => [
        'BRAND_REF.HIGHLOAD.UF_XML_ID' => 'BRAND_1',
    ],
    'order' => ['ID' => 'ASC'],
]);

$arElements = [];
while ($obElement = $rsElement->fetchObject()) {
    $brandNames = [];
    // BRAND_REF множественное свойство, по этому необходимо получить все значения
    foreach ($obElement->getBrandRef()->getAll() as $obBrandRef) {
        $brandNames[] = $obBrandRef->getHighload()->getUfName();
    }

    $arElements[] = [
        'ID' => $obElement->getId(),
        'NAME' => $obElement->getName(),
        'BRAND' => $brandNames,
    ];
}
