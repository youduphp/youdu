<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduPhp\Youdu\Application;

beforeEach(function () {
    $config = makeConfig();
    $this->session = (new Application($config))->session();
});

it('assert get session info', function () {
    expect($this->session)->toBeInstanceOf(\YouduPhp\Youdu\Kernel\Session\Client::class);
});
