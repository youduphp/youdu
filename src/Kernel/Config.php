<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Kernel;

class Config
{
    public function __construct(protected array $config = [])
    {
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

    public function getTimeout(): int|float
    {
        return $this->get('timeout', 5);
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
}
