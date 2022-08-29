<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduPhp\Youdu\Generator\AccessTokenGenerator;
use YouduPhp\Youdu\Http\ClientInterface;
use YouduPhp\Youdu\Packer\Packer;

beforeEach(function () {
    $this->accessToken = md5((string) time());

    $config = makeConfig();
    $packer = new Packer($config);
    $encrypt = $packer->pack(json_encode([
        'accessToken' => $this->accessToken,
    ]));

    $client = mock(ClientInterface::class)->expect(
        post: fn () => [
            'errcode' => 0,
            'encrypt' => $encrypt,
        ]
    );

    $this->accessTokenGenerator = new AccessTokenGenerator($config, $client, $packer);
});

it('asserts accessTokenGenerator', function () {
    expect($this->accessTokenGenerator->generate())->toBeString()->toBe($this->accessToken);
});
