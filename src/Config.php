<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduSdk\Youdu;

use YouduSdk\Youdu\Crypt\Prpcrypt;
use YouduSdk\Youdu\Exceptions\ErrorCode;
use YouduSdk\Youdu\Exceptions\Exception;

class Config
{
    protected string $api = '';

    protected int $buin = 0;

    protected string $appId = '';

    protected string $aesKey = '';

    protected string $tmpPath = '/tmp';

    protected Prpcrypt $crypter;

    public function __construct(array $config)
    {
        $this->api = $config['api'] ?? '';
        $this->buin = $config['buin'] ?? '';
        $this->appId = $config['appId'] ?? '';
        $this->aesKey = $config['aes_key'] ?? '';
        $this->tmpPath = $config['tmp_path'] ?? '';
        $this->crypter = new Prpcrypt($config['aes_key'] ?? '');
    }

    public function getApi(): string
    {
        return $this->api;
    }

    public function getBuin(): int
    {
        return $this->buin;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getAesKey(): string
    {
        return $this->aesKey;
    }

    public function getTmpPath(): string
    {
        return $this->tmpPath;
    }

    public function getCrypter(): Prpcrypt
    {
        return $this->crypter;
    }

    /**
     * 加密.
     */
    public function encryptMsg(string $unencrypted = ''): string
    {
        [$errcode, $encrypted] = $this->crypter->encrypt($unencrypted, $this->appId);

        if ($errcode != 0) {
            throw new Exception($encrypted, $errcode);
        }

        return $encrypted;
    }

    /**
     * 解密.
     */
    public function decryptMsg(?string $encrypted): string
    {
        if (strlen($this->aesKey) != 44) {
            throw new Exception('Illegal aesKey', ErrorCode::$IllegalAesKey);
        }

        [$errcode, $decrypted] = $this->crypter->decrypt($encrypted, $this->appId);

        if ($errcode != 0) {
            throw new Exception('Decrypt failed:' . $decrypted, (int) $errcode);
        }

        return $decrypted;
    }
}
