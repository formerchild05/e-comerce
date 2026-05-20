<?php

namespace Minh\VNPay\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Minh\VNPay\Model\Payment\VNPay;

class ConfigProvider implements ConfigProviderInterface
{
    private VNPay $method;
    private Escaper $escaper;
    private UrlInterface $urlBuilder;

    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper,
        UrlInterface $urlBuilder
    ) {
        $this->method = $paymentHelper->getMethodInstance(VNPay::CODE);
        $this->escaper = $escaper;
        $this->urlBuilder = $urlBuilder;
    }

    public function getConfig(): array
    {
        if (!$this->method->isAvailable()) {
            return [];
        }

        return [
            'payment' => [
                'minh_vnpay' => [
                    'description' => $this->escaper->escapeHtml(
                        __('You will be redirected to VNPay to complete payment.')
                    ),
                    'startUrl' => $this->urlBuilder->getUrl('vnpay/payment/start', ['_secure' => true]),
                ],
            ],
        ];
    }
}
