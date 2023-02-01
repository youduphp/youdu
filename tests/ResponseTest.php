<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduPhp\Youdu\Kernel\HttpClient\Response;
use YouduPhp\Youdu\Kernel\Utils\Packer\Packer;

beforeEach(function () {
    $config = makeConfig();
    $this->packer = new Packer($config);
});
it('assert response', function () {
    $body = [
        'errcode' => 0,
        'errmsg' => '',
        'encrypt' => $this->packer->pack(json_encode(['foo' => 'bar', 'bar' => ['baz' => 'qux']])),
    ];
    $psrResponse = new \GuzzleHttp\Psr7\Response(200, [], json_encode($body));

    $response = new Response($psrResponse, $this->packer);

    expect($response->json())->toBeArray();
    expect($response->json('foo'))->toBe('bar');
    expect($response->json('bar.baz'))->toBe('qux');
});
