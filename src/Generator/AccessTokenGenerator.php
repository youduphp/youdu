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
use YouduSdk\Youdu\Http\ClientInterface;
use YouduSdk\Youdu\Packer\PackerInterface;

class AccessTokenGenerator
{
    protected ClientInterface $client;

    protected PackerInterface $packer;

    public function __construct(protected Config $config)
    {
        $this->client = $config->getClient();
        $this->packer = $config->getPacker();
    }

    public function generate(): string
    {
        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $this->packer->pack((string) time()),
        ];

        $response = $this->client->post('/cgi/gettoken', $parameters);

        if ($response['errcode'] != 0) {
            throw new RuntimeException($response['errmsg'], $response['errcode']);
        }

        $decrypted = $this->packer->unpack($response['encrypt']);
        $decoded = json_decode($decrypted, true);

        return $decoded['accessToken'];
    }
}
