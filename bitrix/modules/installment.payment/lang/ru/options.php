<?php
$MESS["INSTALLMENT_PAYMENT_OPTIONS_TITLE"] = "Настройки модуля \"Оплата частями\"";
$MESS["INSTALLMENT_PAYMENT_OPTIONS_TAB_GENERAL"] = "Общие";
$MESS["INSTALLMENT_PAYMENT_OPTIONS_TAB_GENERAL_TITLE"] = "Общие настройки";
$MESS["INSTALLMENT_PAYMENT_OPTIONS_TAB_MARKUP"] = "Проценты наценки";
$MESS["INSTALLMENT_PAYMENT_OPTIONS_TAB_MARKUP_TITLE"] = "Настройка процентов наценки для разных периодов оплаты";
$MESS["INSTALLMENT_PAYMENT_OPTIONS_MIN_ORDER_AMOUNT"] = "Минимальная сумма заказа";
$MESS["INSTALLMENT_PAYMENT_OPTIONS_DETAILS_LINK"] = "Ссылка на страницу с подробной информацией";
$MESS["INSTALLMENT_PAYMENT_OPTIONS_MARKUP_PERIOD"] = "Наценка для периода";
$MESS["INSTALLMENT_PAYMENT_OPTIONS_MONTHS"] = "месяцев";
$MESS["INSTALLMENT_PAYMENT_OPTIONS_NOTE"] = "
    <div style=\"margin: 8px 0;\">
        <div style=\"font-weight: bold; margin-bottom: 5px;\">Инструкция по настройке:</div>
        <div style=\"margin-left: 10px;\">1. Установите минимальную сумму заказа, доступную для оплаты частями</div>
        <div style=\"margin-left: 10px;\">2. Укажите ссылку на страницу с подробной информацией об условиях оплаты частями</div>
        <div style=\"margin-left: 10px;\">3. Настройте проценты наценки для разных периодов оплаты (0% означает оплату без переплат)</div>
        <div style=\"margin-top: 8px;\">Для добавления кнопки оплаты частями на страницу товара, вставьте следующий код в шаблон:</div>
        <div style=\"background-color: #f5f5f5; padding: 8px; margin: 5px 0; font-family: monospace; border: 1px solid #ddd;\">
        &lt;?\\$APPLICATION->IncludeComponent(<br>
        &nbsp;&nbsp;&nbsp;&nbsp;\"installment:payment.button\",<br>
        &nbsp;&nbsp;&nbsp;&nbsp;\"\",<br>
        &nbsp;&nbsp;&nbsp;&nbsp;Array(<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"PRODUCT_ID\" => \$arResult[\"ID\"],<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"PRODUCT_PRICE\" => \$arResult[\"MIN_PRICE\"][\"DISCOUNT_VALUE\"],<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"PRODUCT_NAME\" => \$arResult[\"NAME\"],<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"PRODUCT_IMAGE\" => \$arResult[\"DETAIL_PICTURE\"][\"SRC\"]<br>
        &nbsp;&nbsp;&nbsp;&nbsp;)<br>
        );?&gt;
        </div>
    </div>
";
?>