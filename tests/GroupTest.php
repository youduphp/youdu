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
    $this->group = (new Application($config))->group();
});

it('assert get group info', function () {
    expect($this->group)->toBeInstanceOf(\YouduPhp\Youdu\Kernel\Group\Client::class);
});
