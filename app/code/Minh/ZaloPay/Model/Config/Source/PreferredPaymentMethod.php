<?php

namespace Minh\ZaloPay\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PreferredPaymentMethod implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => '', 'label' => __('All Methods')],
            ['value' => 'domestic_card,account', 'label' => __('ATM Cards / Bank Account')],
            ['value' => 'international_card', 'label' => __('Credit / Debit Cards')],
            ['value' => 'zalopay_wallet', 'label' => __('ZaloPay Wallet QR')],
            ['value' => 'vietqr', 'label' => __('VietQR')],
        ];
    }
}
