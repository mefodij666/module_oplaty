<?php
namespace Installment\Payment;

use Bitrix\Main\Config\Option;

/**
 * Класс для расчета платежей по рассрочке
 */
class Calculator
{
    private $moduleId = 'installment.payment';
    private $productPrice = 0;
    private $initialPaymentPercent = 0;
    private $installmentPeriod = 6;
    
    /**
     * Конструктор
     *
     * @param float $productPrice Цена товара
     * @param float $initialPaymentPercent Процент первоначального платежа (0-50)
     * @param int $installmentPeriod Период рассрочки в месяцах
     */
    public function __construct($productPrice, $initialPaymentPercent = 0, $installmentPeriod = 6)
    {
        $this->productPrice = floatval($productPrice);
        $this->initialPaymentPercent = floatval($initialPaymentPercent);
        $this->installmentPeriod = intval($installmentPeriod);
    }
    
    /**
     * Расчет суммы первоначального платежа
     *
     * @return float
     */
    public function calculateInitialPayment()
    {
        return ($this->initialPaymentPercent / 100) * $this->productPrice;
    }
    
    /**
     * Расчет ежемесячного платежа
     *
     * @return float
     */
    public function calculateMonthlyPayment()
    {
        $initialPayment = $this->calculateInitialPayment();
        $remainingAmount = $this->productPrice - $initialPayment;
        
        // Получаем процент наценки для выбранного периода
        $markupPercent = $this->getMarkupPercent($this->installmentPeriod);
        
        // Рассчитываем сумму с наценкой
        $totalWithMarkup = $remainingAmount * (1 + $markupPercent / 100);
        
        // Рассчитываем ежемесячный платеж
        return $totalWithMarkup / $this->installmentPeriod;
    }
    
    /**
     * Расчет итоговой стоимости при оплате частями
     *
     * @return float
     */
    public function calculateTotalPrice()
    {
        $initialPayment = $this->calculateInitialPayment();
        $remainingAmount = $this->productPrice - $initialPayment;
        
        // Получаем процент наценки для выбранного периода
        $markupPercent = $this->getMarkupPercent($this->installmentPeriod);
        
        // Рассчитываем сумму с наценкой
        $totalWithMarkup = $remainingAmount * (1 + $markupPercent / 100);
        
        return $initialPayment + $totalWithMarkup;
    }
    
    /**
     * Получить процент наценки для указанного периода
     *
     * @param int $period Период в месяцах
     * @return float Процент наценки
     */
    public function getMarkupPercent($period)
    {
        // По умолчанию 0%
        $markup = 0;
        
        // Получаем из настроек модуля
        if (Option::get($this->moduleId, "MARKUP_PERCENT_" . $period) !== null) {
            $markup = floatval(Option::get($this->moduleId, "MARKUP_PERCENT_" . $period));
        } else {
            // Если нет точного соответствия, используем ближайший период
            $periods = [2, 3, 4, 5, 6, 9, 12, 18, 24, 36, 48];
            $nearest = $this->getNearestPeriod($period, $periods);
            if ($nearest) {
                $markup = floatval(Option::get($this->moduleId, "MARKUP_PERCENT_" . $nearest));
            }
        }
        
        return $markup;
    }
    
    /**
     * Получить ближайший доступный период
     *
     * @param int $period Запрашиваемый период
     * @param array $availablePeriods Массив доступных периодов
     * @return int|null
     */
    private function getNearestPeriod($period, $availablePeriods)
    {
        if (in_array($period, $availablePeriods)) {
            return $period;
        }
        
        $nearest = null;
        $minDiff = PHP_INT_MAX;
        
        foreach ($availablePeriods as $available) {
            $diff = abs($period - $available);
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $nearest = $available;
            }
        }
        
        return $nearest;
    }
    
    /**
     * Форматирование суммы в валюте
     *
     * @param float $amount Сумма
     * @param bool $useRubShort Использовать сокращение 'р.' вместо 'руб.'
     * @return string
     */
    public static function formatCurrency($amount, $useRubShort = false)
    {
        $formatted = number_format($amount, 2, ',', ' ');
        // Убираем нули после запятой, если сумма целая
        $formatted = preg_replace('/,00$/', '', $formatted);
        
        return $formatted . ($useRubShort ? ' р.' : ' руб.');
    }
}