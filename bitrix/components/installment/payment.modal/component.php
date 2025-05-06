<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

if (!Loader::includeModule('installment.payment')) {
    ShowError(Loc::getMessage('INSTALLMENT_PAYMENT_MODULE_NOT_INSTALLED'));
    return;
}

// Get available payment periods
$paymentPeriods = [2, 3, 4, 5, 6, 9, 12, 18, 24, 36, 48];

// Get down payment percentages
$downPaymentPercentages = [0, 10, 20, 30, 40, 50];

// Get markup percentage from module settings
$markupPercentage = floatval(Option::get('installment.payment', 'MARKUP_PERCENTAGE', 5));

// Prepare component result
$arResult = array(
    'PAYMENT_PERIODS' => $paymentPeriods,
    'DOWN_PAYMENT_PERCENTAGES' => $downPaymentPercentages,
    'MARKUP_PERCENTAGE' => $markupPercentage,
    'DEFAULT_PAYMENT_PERIOD' => 6, // Default selected payment period
    'DEFAULT_DOWN_PAYMENT' => 0    // Default selected down payment percentage
);

$this->IncludeComponentTemplate();
