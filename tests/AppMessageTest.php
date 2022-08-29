<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduPhp\Youdu\Message\App\MessageInterface;
use YouduPhp\Youdu\Message\App\Text;

it('assert app messate', function () {
    expect(new Text('hello'))->toBeInstanceOf(MessageInterface::class);
});
