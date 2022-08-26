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
        $this->crypter = new Prpcrypt($this->appId, $this->aesKey);
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
}
