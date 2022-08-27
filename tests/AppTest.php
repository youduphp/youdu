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
use YouduPhp\Youdu\Config;
use YouduPhp\Youdu\Http\ClientInterface;

beforeEach(function () {
    $config = new Config([], mock(ClientInterface::class)->expect());
    $this->app = new App($config);
});
