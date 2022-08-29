<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu;

class Config
{
    public function __construct(protected array $config = [])
    {
    }

    public function getApi(): string
    {
        return $this->config['api'] ?? '';
    }

    public function getTimeout(): int|float
    {
        return $this->config['timeout'] ?? 5;
    }

    public function getBuin(): int
    {
        return (int) $this->config['buin'] ?? 0;
    }

    public function getAppId(): string
    {
        return $this->config['app_id'] ?? '';
    }

    public function getAesKey(): string
    {
        return $this->config['aes_key'] ?? '';
    }

    public function getTmpPath(): string
    {
        return $this->config['tmp_path'] ?? '/tmp';
    }
}
