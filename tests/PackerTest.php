<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduPhp\Youdu\Kernel\Utils\Packer\Packer;

beforeEach(function () {
    $config = makeConfig();
    $this->packer = new Packer($config);
    $this->str = 'hello';
});

it('asserts packer', function () {
    expect($packed = $this->packer->pack($this->str))->toBeString();
    expect($this->packer->unpack($packed))->toBe($this->str);
});
