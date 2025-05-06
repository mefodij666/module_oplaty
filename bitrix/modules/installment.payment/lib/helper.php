<?php
namespace Installment\Payment;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

/**
 * Helper class for installment payment module
 */
class Helper
{
    /**
     * Get module settings
     * 
     * @return array - Module settings
     */
    public static function getSettings()
    {
        $moduleId = 'installment.payment';
        
        return array(
            'MARKUP_PERCENTAGE' => floatval(Option::get($moduleId, 'MARKUP_PERCENTAGE', 5))
        );
    }
    
    /**
     * Get available payment periods
     * 
     * @return array - Payment periods
     */
    public static function getPaymentPeriods()
    {
        return array(2, 3, 4, 5, 6, 9, 12, 18, 24, 36, 48);
    }
    
    /**
     * Get available down payment percentages
     * 
     * @return array - Down payment percentages
     */
    public static function getDownPaymentPercentages()
    {
        return array(0, 10, 20, 30, 40, 50);
    }
    
    /**
     * Check if product is eligible for installment payment
     * 
     * @param int $productId - Product ID
     * @return bool - True if product is eligible
     */
    public static function isProductEligible($productId)
    {
        // Check if product exists
        if (!$productId) {
            return false;
        }
        
        // Check if product is available
        if (Loader::includeModule('catalog')) {
            $product = \CCatalogProduct::GetByID($productId);
            if (!$product || $product['QUANTITY'] <= 0) {
                return false;
            }
            
            // Check if product price is above minimum
            $arPrice = \CCatalogProduct::GetOptimalPrice($productId, 1, array(2), 'N');
            if (empty($arPrice) || $arPrice['DISCOUNT_PRICE'] < 100) {
                return false;
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Format price with currency
     * 
     * @param float $price - Price value
     * @param string $currency - Currency code
     * @return string - Formatted price
     */
    public static function formatPrice($price, $currency = 'RUB')
    {
        if (Loader::includeModule('currency')) {
            return \CCurrencyLang::CurrencyFormat($price, $currency);
        }
        
        // Simple formatting if currency module is not available
        return number_format($price, 2, ',', ' ') . ' ' . ($currency === 'RUB' ? 'р.' : $currency);
    }
    
    /**
     * Add installment payment button to product detail page
     * 
     * @param string $content - Page content
     * @return string - Modified content
     */
    public static function addInstallmentButtonToPage($content)
    {
        // Find position after "Buy in 1 click" button
        $buyInOneClickPos = strpos($content, 'Купить в 1 клик');
        if ($buyInOneClickPos === false) {
            return $content;
        }
        
        // Find the end of the button container
        $endPos = strpos($content, '</div>', $buyInOneClickPos);
        if ($endPos === false) {
            return $content;
        }
        
        // Get installment button HTML
        $installmentButton = '<div class="installment-payment-button-container">
            <button class="btn btn-installment">
                Оплата частями: <span class="installment-zero-today">0 р. сегодня</span>
            </button>
        </div>';
        
        // Insert button after "Buy in 1 click" button
        $content = substr_replace($content, $installmentButton, $endPos + 6, 0);
        
        return $content;
    }
}
