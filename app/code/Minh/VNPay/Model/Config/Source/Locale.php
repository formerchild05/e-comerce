<?php

namespace Minh\VNPay\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Locale implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'vn', 'label' => __('Vietnamese')],
            ['value' => 'en', 'label' => __('English')],
        ];
    }
}
