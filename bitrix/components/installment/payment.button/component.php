<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule('installment.payment')) {
    ShowError(Loc::getMessage('INSTALLMENT_PAYMENT_MODULE_NOT_INSTALLED'));
    return;
}

if (!Loader::includeModule('catalog')) {
    ShowError(Loc::getMessage('CATALOG_MODULE_NOT_INSTALLED'));
    return;
}

// Get product ID from parameters
$productId = intval($arParams['PRODUCT_ID']);
if ($productId <= 0) {
    ShowError(Loc::getMessage('INSTALLMENT_PRODUCT_ID_NOT_SET'));
    return;
}

// Get product information
$productPrice = 0;
$productName = '';

// Load product data
$res = CIBlockElement::GetByID($productId);
if ($ar_res = $res->GetNext()) {
    $productName = $ar_res['NAME'];
    
    // Get product price
    $arPrice = CCatalogProduct::GetOptimalPrice($productId, 1, array(2), 'N');
    if (!empty($arPrice)) {
        $productPrice = $arPrice['DISCOUNT_PRICE'];
    }
}

// Calculate minimum monthly payment (maximum period - 48 months)
$minMonthlyPayment = $productPrice / 48;

// Prepare component result
$arResult = array(
    'PRODUCT_ID' => $productId,
    'PRODUCT_NAME' => $productName,
    'PRODUCT_PRICE' => $productPrice,
    'MIN_MONTHLY_PAYMENT' => $minMonthlyPayment,
    'CURRENCY' => CCurrency::GetBaseCurrency()
);

$this->IncludeComponentTemplate();
