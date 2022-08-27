<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduSdk\Youdu\Config;
use YouduSdk\Youdu\Generator\AccessTokenGenerator;
use YouduSdk\Youdu\Http\ClientInterface;

beforeEach(function () {
    $this->config = new Config([
        'api' => 'http://127.0.0.1:8888',
        'timeout' => 5.0,
        'buin' => 123,
        'app_id' => 'app_id',
        'aes_key' => 'aes_key',
    ], mock(ClientInterface::class)->expect());
});

test('Test Config', function () {
    expect($this->config->getApi())->toEqual('http://127.0.0.1:8888');
    expect($this->config->get('timeout'))->toEqual(5.0);
    expect($this->config->getBuin())->toEqual(123);
    expect($this->config->getAppId())->toEqual('app_id');
    expect($this->config->getAesKey())->toEqual('aes_key');
    expect($this->config->getClient())->toBeInstanceOf(ClientInterface::class);
    expect($this->config->getAccessTokenGenerator())->toBeInstanceOf(AccessTokenGenerator::class);
});
