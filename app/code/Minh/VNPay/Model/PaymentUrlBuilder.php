<?php

namespace Minh\VNPay\Model;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;

class PaymentUrlBuilder
{
    private const VNPAY_TIMEZONE = 'Asia/Ho_Chi_Minh';
    private const PAYMENT_TIMEOUT_SECONDS = 1800;

    private VNPayConfig $config;
    private Signature $signature;
    private UrlInterface $urlBuilder;
    private RemoteAddress $remoteAddress;

    public function __construct(
        VNPayConfig $config,
        Signature $signature,
        UrlInterface $urlBuilder,
        RemoteAddress $remoteAddress
    ) {
        $this->config = $config;
        $this->signature = $signature;
        $this->urlBuilder = $urlBuilder;
        $this->remoteAddress = $remoteAddress;
    }

    public function build(OrderInterface $order): string
    {
        $storeId = $order->getStoreId();
        $paymentUrl = (string) $this->config->getValue('payment_url', $storeId);
        $tmnCode = trim((string) $this->config->getValue('tmn_code', $storeId));
        $hashSecret = $this->config->getHashSecret($storeId);

        if ($paymentUrl === '' || $tmnCode === '' || $hashSecret === '') {
            throw new LocalizedException(__('VNPay payment is not configured.'));
        }

        $amount = (int) round(((float) $order->getGrandTotal()) * 100);
        $createdAt = new \DateTimeImmutable('now', new \DateTimeZone(self::VNPAY_TIMEZONE));
        $expiresAt = $createdAt->modify('+' . self::PAYMENT_TIMEOUT_SECONDS . ' seconds');
        $returnUrl = trim((string) $this->config->getValue('return_url_override', $storeId));
        if ($returnUrl === '') {
            $returnUrl = $this->urlBuilder->getUrl('vnpay/payment/return', ['_secure' => true]);
        }

        $params = [
            'vnp_Version' => $this->config->getValue('version', $storeId) ?: '2.1.0',
            'vnp_Command' => $this->config->getValue('command', $storeId) ?: 'pay',
            'vnp_TmnCode' => $tmnCode,
            'vnp_Amount' => $amount,
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => $order->getIncrementId(),
            'vnp_OrderInfo' => 'Thanh toan don hang ' . $order->getIncrementId(),
            'vnp_OrderType' => $this->config->getValue('order_type', $storeId) ?: 'other',
            'vnp_Locale' => $this->config->getValue('locale', $storeId) ?: 'vn',
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_IpAddr' => $this->remoteAddress->getRemoteAddress() ?: '127.0.0.1',
            'vnp_CreateDate' => $createdAt->format('YmdHis'),
            'vnp_ExpireDate' => $expiresAt->format('YmdHis'),
        ];
        $params['vnp_SecureHash'] = $this->signature->sign($params, $hashSecret);

        return $paymentUrl . '?' . $this->signature->buildQuery($params);
    }
}
