<?php

namespace Minh\VNPay\Model;

class Signature
{
    public function sign(array $params, string $hashSecret): string
    {
        $params = $this->prepareParams($params);

        return hash_hmac('sha512', $this->buildHashData($params), $hashSecret);
    }

    public function verify(array $params, string $hashSecret): bool
    {
        if (empty($params['vnp_SecureHash'])) {
            return false;
        }

        return hash_equals(strtolower((string) $params['vnp_SecureHash']), $this->sign($params, $hashSecret));
    }

    public function buildQuery(array $params): string
    {
        $query = http_build_query($this->prepareParams($params), '', '&', PHP_QUERY_RFC1738);

        if (!empty($params['vnp_SecureHash'])) {
            $query .= '&vnp_SecureHash=' . urlencode((string) $params['vnp_SecureHash']);
        }

        return $query;
    }

    private function prepareParams(array $params): array
    {
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        $params = array_filter(
            $params,
            static function ($value, string $key): bool {
                return str_starts_with($key, 'vnp_') && $value !== null && $value !== '';
            },
            ARRAY_FILTER_USE_BOTH
        );

        ksort($params);

        return $params;
    }

    private function buildHashData(array $params): string
    {
        $hashData = [];

        foreach ($params as $key => $value) {
            $hashData[] = urlencode((string) $key) . '=' . urlencode((string) $value);
        }

        return implode('&', $hashData);
    }
}
