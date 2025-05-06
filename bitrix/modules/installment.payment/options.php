<?php
/** 
 * Настройки модуля "Оплата частями"
 * 
 * @package installment.payment
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;

$module_id = 'installment.payment';

// Подключаем языковые файлы
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
Loc::loadMessages(__FILE__);

// Проверка прав доступа
$APPLICATION->SetTitle(Loc::getMessage("INSTALLMENT_PAYMENT_OPTIONS_TITLE"));
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

// Получаем доступ к обработчику запросов
$request = HttpApplication::getInstance()->getContext()->getRequest();

// Подготавливаем доступные периоды
$periods = array(2, 3, 4, 5, 6, 9, 12, 18, 24, 36, 48);

// Инициализируем вкладки
$aTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("INSTALLMENT_PAYMENT_OPTIONS_TAB_GENERAL"),
        "ICON" => "installment_payment_settings",
        "TITLE" => Loc::getMessage("INSTALLMENT_PAYMENT_OPTIONS_TAB_GENERAL_TITLE")
    ),
    array(
        "DIV" => "edit2",
        "TAB" => Loc::getMessage("INSTALLMENT_PAYMENT_OPTIONS_TAB_MARKUP"),
        "ICON" => "installment_payment_settings",
        "TITLE" => Loc::getMessage("INSTALLMENT_PAYMENT_OPTIONS_TAB_MARKUP_TITLE")
    ),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();

// Проверяем, была ли отправлена форма
if ($request->isPost() && check_bitrix_sessid()) {
    if ($request->getPost('save') || $request->getPost('apply')) {
        // Общие настройки
        Option::set($module_id, "MIN_ORDER_AMOUNT", $request->getPost('MIN_ORDER_AMOUNT'));
        Option::set($module_id, "DETAILS_LINK", $request->getPost('DETAILS_LINK'));
        
        // Настройки процентов наценки для разных периодов
        foreach ($periods as $period) {
            $markupField = "MARKUP_PERCENT_" . $period;
            $markupValue = $request->getPost($markupField);
            Option::set($module_id, $markupField, $markupValue);
        }
        
        // Redirect back to options page
        LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . $module_id . "&lang=" . LANGUAGE_ID . "&" . $tabControl->ActiveTabParam() . "&" . ($request->getPost('apply') ? "mid=".$module_id : ""));
    }
}

// Начало формы
?>
<form method="post" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($module_id) ?>&lang=<?= LANGUAGE_ID ?>">
<?= bitrix_sessid_post() ?>
<?php
// Вкладка общих настроек
$tabControl->BeginNextTab();
?>
    <tr>
        <td width="40%">
            <label for="MIN_ORDER_AMOUNT"><?= Loc::getMessage("INSTALLMENT_PAYMENT_OPTIONS_MIN_ORDER_AMOUNT") ?>:</label>
        </td>
        <td width="60%">
            <input type="text" name="MIN_ORDER_AMOUNT" id="MIN_ORDER_AMOUNT" 
                   value="<?= htmlspecialcharsbx(Option::get($module_id, "MIN_ORDER_AMOUNT", 100)) ?>" 
                   size="10">
        </td>
    </tr>
    <tr>
        <td width="40%">
            <label for="DETAILS_LINK"><?= Loc::getMessage("INSTALLMENT_PAYMENT_OPTIONS_DETAILS_LINK") ?>:</label>
        </td>
        <td width="60%">
            <input type="text" name="DETAILS_LINK" id="DETAILS_LINK" 
                   value="<?= htmlspecialcharsbx(Option::get($module_id, "DETAILS_LINK", "/installment-info/")) ?>" 
                   size="40">
        </td>
    </tr>
<?php
// Вкладка настроек наценки для разных периодов
$tabControl->BeginNextTab();

foreach ($periods as $period):
    $markupField = "MARKUP_PERCENT_" . $period;
    $markupValue = Option::get($module_id, $markupField, "0");
?>
    <tr>
        <td width="40%">
            <label for="<?= $markupField ?>"><?= Loc::getMessage("INSTALLMENT_PAYMENT_OPTIONS_MARKUP_PERIOD") . " " . $period . " " . GetMessage("INSTALLMENT_PAYMENT_OPTIONS_MONTHS") ?>:</label>
        </td>
        <td width="60%">
            <input type="text" name="<?= $markupField ?>" id="<?= $markupField ?>" 
                   value="<?= htmlspecialcharsbx($markupValue) ?>" 
                   size="5"> %
        </td>
    </tr>
<?php 
endforeach;

// Кнопки сохранения
$tabControl->Buttons();
?>
    <input type="submit" name="save" value="<?= Loc::getMessage("MAIN_SAVE") ?>" class="adm-btn-save">
    <input type="submit" name="apply" value="<?= Loc::getMessage("MAIN_APPLY") ?>" class="adm-btn-save">
    <input type="reset" name="reset" value="<?= Loc::getMessage("MAIN_RESET") ?>">
<?php
$tabControl->End();
?>
</form>
<?php
// Показываем информацию о настройках
echo BeginNote();
echo Loc::getMessage("INSTALLMENT_PAYMENT_OPTIONS_NOTE");
echo EndNote();
?>