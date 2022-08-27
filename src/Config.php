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

use YouduPhp\Youdu\Generator\AccessTokenGenerator;
use YouduPhp\Youdu\Generator\UrlGenerator;
use YouduPhp\Youdu\Http\ClientInterface;
use YouduPhp\Youdu\Packer\MessagePacker;
use YouduPhp\Youdu\Packer\PackerInterface;

class Config
{
    protected array $config = [];

    protected PackerInterface $packer;

    protected AccessTokenGenerator $accessTokenGenerator;

    protected UrlGenerator $urlGenerator;

    public function __construct(array $config = [], protected ?ClientInterface $client = null)
    {
        $this->config = array_merge([
            'api' => '',
            'buin' => 0,
            'appId' => '',
            'aes_key' => '',
            'tmp_path' => '',
        ], $config);
        $this->packer = new MessagePacker($this);
        $this->accessTokenGenerator = new AccessTokenGenerator($this);
        $this->urlGenerator = new UrlGenerator($this->accessTokenGenerator);
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

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getClient(): ?ClientInterface
    {
        return $this->client;
    }

    public function getAccessTokenGenerator(): AccessTokenGenerator
    {
        return $this->accessTokenGenerator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }
}
