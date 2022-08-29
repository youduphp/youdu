<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduPhp\Youdu\App;
use YouduPhp\Youdu\Generator\AccessTokenGenerator;
use YouduPhp\Youdu\Generator\UrlGenerator;
use YouduPhp\Youdu\Http\ClientInterface;
use YouduPhp\Youdu\Packer\Packer;

beforeEach(function () {
    $config = makeConfig();
    $client = mock(ClientInterface::class)->expect();
    $packer = new Packer($config);
    $accessTokenGenerator = new AccessTokenGenerator($config, $client, $packer);
    $urlGenerator = new UrlGenerator($accessTokenGenerator);
    $this->app = new App($config, $client, $packer, $urlGenerator);
});

it('asserts send message', function () {
    expect($this->app)->toBeInstanceOf(App::class);
});
