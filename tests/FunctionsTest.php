<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use function YouduPhp\Youdu\Kernel\Util\array_get;

it('assert array_get', function () {
    expect(array_get(['a' => 'b'], 'a'))->toBe('b');
    expect(array_get(['a' => ['b' => 'c']], 'a'))->toBe(['b' => 'c']);
    expect(array_get(['a' => ['b' => 'c']], 'a.b'))->toBe('c');
    expect(array_get(['a' => ['b' => 'c']], 'a.c'))->toBe(null);
    expect(array_get(['a' => ['b' => 'c']], 'a.c', 'd'))->toBe('d');
});
