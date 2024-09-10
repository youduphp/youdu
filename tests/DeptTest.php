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
    $this->dept = (new Application($config))->dept();
});

it('asserts dept', function () {
    expect($this->dept)->toBeInstanceOf(YouduPhp\Youdu\Kernel\Dept\Client::class);
});
