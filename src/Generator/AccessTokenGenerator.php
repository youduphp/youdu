<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduSdk\Youdu\Generator;

use RuntimeException;
use YouduSdk\Youdu\Config;

class AccessTokenGenerator
{
    public function __construct(protected Config $config)
    {
    }

    public function generate(): string
    {
        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $this->config->getPacker()->pack((string) time()),
        ];

        $response = $this->config->getClient()->post('/cgi/gettoken', $parameters);

        if ($response['errcode'] != 0) {
            throw new RuntimeException($response['errmsg'], $response['errcode']);
        }

        $decrypted = $this->config->getPacker()->unpack($response['encrypt']);
        $decoded = json_decode($decrypted, true);

        return $decoded['accessToken'];
    }
}
