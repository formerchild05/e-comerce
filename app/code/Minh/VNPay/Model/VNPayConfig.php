<?php

namespace Minh\VNPay\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use Minh\VNPay\Model\Payment\VNPay;

class VNPayConfig
{
    private ScopeConfigInterface $scopeConfig;
    private EncryptorInterface $encryptor;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    public function getValue(string $field, $storeId = null): ?string
    {
        $value = $this->scopeConfig->getValue(
            'payment/' . VNPay::CODE . '/' . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value !== null ? (string) $value : null;
    }

    public function getHashSecret($storeId = null): string
    {
        $value = (string) $this->getValue('hash_secret', $storeId);

        try {
            $decrypted = $this->encryptor->decrypt($value);
            return $decrypted !== '' ? $decrypted : $value;
        } catch (\Exception $exception) {
            return $value;
        }
    }

    public function isDebug($storeId = null): bool
    {
        return $this->getValue('debug', $storeId) === '1';
    }
}
