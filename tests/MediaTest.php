<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduPhp\Youdu\Application;

beforeEach(function () {
    $config = makeConfig();
    $this->media = (new Application($config))->media();
});

it('assert get media info', function () {
    expect($this->media)->toBeInstanceOf(YouduPhp\Youdu\Kernel\Media\Client::class);
    // $mediaId = '43ceeb1bd9fed1fde4983e5b3fb91aba-4';
});
