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

it('assert app message', function () {
    $msg = (new Text('hello'))->toUser('1001');
    expect($msg)
        ->toBeInstanceOf(MessageInterface::class);
    expect($msg->toArray())
        ->toBeArray()
        ->toHaveKeys(['toUser', 'msgType', 'text', 'text.content']);
});
