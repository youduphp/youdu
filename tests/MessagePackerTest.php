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
use YouduPhp\Youdu\Packer\MessagePacker;

beforeEach(function () {
    $this->config = new Config([
        'api' => 'https://127.0.0.1:8888',
        'buin' => 123,
        'app_id' => uniqid(),
        'aes_key' => uniqid(),
        'tmp_path' => '/tmp',
    ]);
});

it('asserts message packer', function () {
    expect($this->config->getPacker())->toBeInstanceOf(MessagePacker::class);
});
