<?php

namespace Minh\ZaloPay\Model;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Gateway
{
    private const VIETNAM_TIMEZONE = 'Asia/Ho_Chi_Minh';
    private const EXPIRE_SECONDS = 900;

    private ZaloPayConfig $config;
    private Signature $signature;
    private Curl $curl;
    private Json $json;
    private UrlInterface $urlBuilder;

    public function __construct(
        ZaloPayConfig $config,
        Signature $signature,
        Curl $curl,
        Json $json,
        UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->signature = $signature;
        $this->curl = $curl;
        $this->json = $json;
        $this->urlBuilder = $urlBuilder;
    }

    public function createOrder(OrderInterface $order): array
    {
        $storeId = $order->getStoreId();
        $appId = (string) $this->config->getValue('app_id', $storeId);
        $appTransId = $this->buildAppTransId((string) $order->getIncrementId());
        $appUser = $this->buildAppUser($order);
        $amount = (int) round((float) $order->getGrandTotal());
        $item = '[]';
        $embedData = $this->json->serialize($this->buildEmbedData($storeId));
        $appTime = (string) round(microtime(true) * 1000);

        $params = [
            'app_id' => $appId,
            'app_user' => $appUser,
            'app_time' => $appTime,
            'amount' => (string) $amount,
            'app_trans_id' => $appTransId,
            'expire_duration_seconds' => (string) self::EXPIRE_SECONDS,
            'embed_data' => $embedData,
            'item' => $item,
            'description' => 'Magento - Thanh toan don hang #' . $order->getIncrementId(),
            'bank_code' => '',
        ];

        $callbackUrl = trim((string) $this->config->getValue('callback_url_override', $storeId));
        if ($callbackUrl === '') {
            $callbackUrl = $this->urlBuilder->getUrl('zalopay/payment/callback', ['_secure' => true]);
        }
        $params['callback_url'] = $callbackUrl;

        $hmacInput = implode('|', [
            $params['app_id'],
            $params['app_trans_id'],
            $params['app_user'],
            $params['amount'],
            $params['app_time'],
            $params['embed_data'],
            $params['item'],
        ]);
        $params['mac'] = $this->signature->sign($hmacInput, $this->config->getKey1($storeId));

        $this->curl->setHeaders(['Content-Type' => 'application/x-www-form-urlencoded']);
        $this->curl->post((string) $this->config->getValue('create_order_url', $storeId), $params);

        $response = $this->json->unserialize($this->curl->getBody());
        $response['request'] = $params;

        return $response;
    }

    public function queryOrder(string $appTransId, $storeId = null): array
    {
        $appId = (string) $this->config->getValue('app_id', $storeId);
        $macInput = $appId . '|' . $appTransId . '|' . $this->config->getKey1($storeId);
        $params = [
            'app_id' => $appId,
            'app_trans_id' => $appTransId,
            'mac' => $this->signature->sign($macInput, $this->config->getKey1($storeId)),
        ];

        $this->curl->setHeaders(['Content-Type' => 'application/x-www-form-urlencoded']);
        $this->curl->post((string) $this->config->getValue('query_order_url', $storeId), $params);

        return $this->json->unserialize($this->curl->getBody());
    }

    private function buildEmbedData($storeId): array
    {
        $embedData = [
            'redirecturl' => trim((string) $this->config->getValue('redirect_url_override', $storeId))
                ?: $this->urlBuilder->getUrl('zalopay/payment/return', ['_secure' => true]),
        ];
        $preferred = trim((string) $this->config->getValue('preferred_payment_method', $storeId));
        if ($preferred !== '') {
            $embedData['preferred_payment_method'] = array_filter(array_map('trim', explode(',', $preferred)));
        } else {
            $embedData['preferred_payment_method'] = [];
        }

        return $embedData;
    }

    private function buildAppTransId(string $incrementId): string
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone(self::VIETNAM_TIMEZONE));
        return $date->format('ymd') . '_' . $incrementId;
    }

    private function buildAppUser(OrderInterface $order): string
    {
        $email = (string) $order->getCustomerEmail();
        return $email !== '' ? substr($email, 0, 50) : 'Magento';
    }
}
