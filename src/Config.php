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

class Config
{
    public function __construct(protected string $api = '', protected int $buin = 0, protected string $appId = '', protected string $aesKey = '')
    {
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
}
