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
    $this->user = (new Application($config))->user();
});

it('assert get user info', function () {
    expect($this->user)->toBeInstanceOf(YouduPhp\Youdu\Kernel\User\Client::class);
});
