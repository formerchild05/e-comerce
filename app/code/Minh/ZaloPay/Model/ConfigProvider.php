<?php

namespace Minh\ZaloPay\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Minh\ZaloPay\Model\Payment\ZaloPay;

class ConfigProvider implements ConfigProviderInterface
{
    private ZaloPay $method;
    private Escaper $escaper;
    private UrlInterface $urlBuilder;

    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper,
        UrlInterface $urlBuilder
    ) {
        $this->method = $paymentHelper->getMethodInstance(ZaloPay::CODE);
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
                'minh_zalopay' => [
                    'description' => $this->escaper->escapeHtml(
                        __('You will be redirected to ZaloPay Gateway to complete payment.')
                    ),
                    'startUrl' => $this->urlBuilder->getUrl('zalopay/payment/start', ['_secure' => true]),
                ],
            ],
        ];
    }
}
