<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

// Load language files
Loc::loadMessages(__FILE__);

// Check access permissions
$moduleId = 'installment.payment';
$modulePermissions = $APPLICATION->GetGroupRight($moduleId);
if ($modulePermissions < "W")
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

// Check if module is installed
if (!Loader::includeModule($moduleId))
{
    $APPLICATION->AuthForm(Loc::getMessage("INSTALLMENT_PAYMENT_MODULE_NOT_INSTALLED"));
}

// Include admin page header
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// Redirect to module settings page
LocalRedirect("/bitrix/admin/settings.php?lang=" . LANGUAGE_ID . "&mid=" . $moduleId . "&mid_menu=1");

// Include admin page footer
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
