<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Generator;

use RuntimeException;
use YouduPhp\Youdu\Config;
use YouduPhp\Youdu\Http\ClientInterface;
use YouduPhp\Youdu\Packer\PackerInterface;

class AccessTokenGenerator
{
    public function __construct(protected Config $config, protected ClientInterface $client, protected PackerInterface $packer)
    {
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
