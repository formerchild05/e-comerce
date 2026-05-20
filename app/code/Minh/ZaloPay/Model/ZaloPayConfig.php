<?php

namespace Minh\ZaloPay\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use Minh\ZaloPay\Model\Payment\ZaloPay;

class ZaloPayConfig
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
            'payment/' . ZaloPay::CODE . '/' . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value !== null ? (string) $value : null;
    }

    public function getKey1($storeId = null): string
    {
        return $this->decryptConfigValue((string) $this->getValue('key1', $storeId));
    }

    public function getKey2($storeId = null): string
    {
        return $this->decryptConfigValue((string) $this->getValue('key2', $storeId));
    }

    private function decryptConfigValue(string $value): string
    {
        try {
            $decrypted = $this->encryptor->decrypt($value);
            return $decrypted !== '' ? $decrypted : $value;
        } catch (\Exception $exception) {
            return $value;
        }
    }
}
