<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduPhp\Youdu\Config;
use YouduPhp\Youdu\Generator\AccessTokenGenerator;
use YouduPhp\Youdu\Http\ClientInterface;

beforeEach(function () {
    $this->config = new Config([
        'api' => 'http://127.0.0.1:8888',
        'timeout' => 5.0,
        'buin' => 123,
        'app_id' => 'app_id',
        'aes_key' => 'aes_key',
    ], mock(ClientInterface::class)->expect());
});

it('asserts config get api')->expect(fn () => $this->config->getApi())->toEqual('http://127.0.0.1:8888');
it('asserts config get timeout')->expect(fn () => $this->config->getTimeout())->toEqual(5.0);
it('asserts config get buin')->expect(fn () => $this->config->getBuin())->toBeInt()->toEqual(123);
it('asserts config get app_id')->expect(fn () => $this->config->getAppId())->toEqual('app_id');
it('asserts config get aes key')->expect(fn () => $this->config->getAesKey())->toEqual('aes_key');
it('asserts config get client')->expect(fn () => $this->config->getClient())->toBeInstanceOf(ClientInterface::class);
it('assert config get access token generator')->expect(fn () => $this->config->getAccessTokenGenerator())->toBeInstanceOf(AccessTokenGenerator::class);
