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

use YouduSdk\Youdu\Packer\MessagePacker;
use YouduSdk\Youdu\Packer\PackerInterface;

class Config
{
    protected array $config = [];

    protected PackerInterface $packer;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'api' => '',
            'buin' => 0,
            'appId' => '',
            'aes_key' => '',
            'tmp_path' => '',
        ], $config);
        $this->packer = new MessagePacker($this);
    }

    public function get(string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        return $this->config[$key] ?? $default;
    }

    public function getApi(): string
    {
        return $this->get('api', '');
    }

    public function getBuin(): int
    {
        return (int) $this->get('buin', 0);
    }

    public function getAppId(): string
    {
        return $this->get('app_id', '');
    }

    public function getAesKey(): string
    {
        return $this->get('aes_key', '');
    }

    public function getTmpPath(): string
    {
        return $this->get('tmp_path', '/tmp');
    }

    public function getPacker(): MessagePacker
    {
        return $this->packer;
    }
}
