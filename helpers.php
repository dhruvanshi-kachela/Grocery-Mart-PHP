<?php
/**
 * Calculates the discounted price.
 *
 * @param float $originalPrice The original price of the item.
 * @param float $discountPercent The discount percentage (e.g., 20 for 20%).
 * @return float The final price after discount, rounded to 2 decimals.
 */
function getDiscountedPrice($originalPrice, $discountPercent) {
    // Ensure inputs are numeric
    if (!is_numeric($originalPrice) || !is_numeric($discountPercent)) {
        return 0.0;
    }
    $discountAmount = $originalPrice * ($discountPercent / 100);
    $finalPrice = $originalPrice - $discountAmount;
    return round($finalPrice, 2);
}
?>
