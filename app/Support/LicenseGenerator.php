<?php

namespace App\Support;

class LicenseGenerator
{
    /**
     * 加密字符串
     *
     * 加密方法
     *
     * @param string $key
     * @param string $cipher
     */
    public function __construct(
        protected string $key,
        protected  string $cipher = 'AES-256-CBC'
    ){}

    /**
     * 加密字符串
     *
     * @param string $value 需要加密的字符串
     * @return string 加密后的字符串
     */
    public function encrypt(string $value): string
    {
        // 生成随机初始化向量
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));

        // 加密数据
        $encrypted = openssl_encrypt($value, $this->cipher, $this->key, 0, $iv);

        if ($encrypted === false) {
            throw new \RuntimeException('加密失败: ' . openssl_error_string());
        }

        // 将 IV 和加密后的数据合并，并进行 base64 编码
        return $this->urlSafeBase64Encode(base64_encode($iv . $encrypted));
    }

    /**
     * 解密字符串
     *
     * @param string $encrypted 加密后的字符串
     * @return string 解密后的原始字符串
     */
    public function decrypt(string $encrypted): string
    {
        // Base64 解码
        $data = base64_decode($this->urlSafeBase64Decode($encrypted));

        if ($data === false) {
            throw new \RuntimeException('解密失败: 无效的 base64 编码');
        }

        // 获取 IV 长度
        $ivLength = openssl_cipher_iv_length($this->cipher);

        if (strlen($data) <= $ivLength) {
            throw new \RuntimeException('解密失败: 数据长度不足');
        }

        // 分离 IV 和加密数据
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        // 解密数据
        $decrypted = openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            throw new \RuntimeException('解密失败: ' . openssl_error_string());
        }

        return $decrypted;
    }

    protected function urlSafeBase64Encode($data): string
    {
        return rtrim(strtr($data, '+/', '-_'), '=');
    }

    // 解码时需要恢复填充
    protected  function urlSafeBase64Decode($base64): false|string
    {
        $base64 = strtr($base64, '-_', '+/');
        $padding = strlen($base64) % 4;
        if ($padding) {
            $base64 .= str_repeat('=', 4 - $padding);
        }
        return base64_decode($base64);
    }
}
