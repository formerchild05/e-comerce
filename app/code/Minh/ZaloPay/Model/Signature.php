<?php

namespace Minh\ZaloPay\Model;

class Signature
{
    public function sign(string $data, string $key): string
    {
        return hash_hmac('sha256', $data, $key);
    }

    public function verify(string $data, string $mac, string $key): bool
    {
        return hash_equals(strtolower($mac), $this->sign($data, $key));
    }
}
