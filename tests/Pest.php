<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduPhp\Youdu\Kernel\Config;

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function makeConfig(): Config
{
    return new Config([
        'api' => getenv('YOUDU_API'),
        'buin' => getenv('YOUDU_BUIN'),
        'app_id' => getenv('YOUDU_DEFAULT_APP_ID'),
        'aes_key' => getenv('YOUDU_DEFAULT_AES_KEY'),
    ]);
}
