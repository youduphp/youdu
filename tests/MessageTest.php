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
use YouduPhp\Youdu\Kernel\Message\Client;

beforeEach(function () {
    $this->message = (new Application(makeConfig()))->message();
});

it('asserts message client', function () {
    expect($this->message)->toBeInstanceOf(Client::class);
});
