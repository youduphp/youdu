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
use YouduPhp\Youdu\Generator\UrlGenerator;
use YouduPhp\Youdu\Http\ClientInterface;
use YouduPhp\Youdu\Packer\PackerInterface;

beforeEach(function () {
    $config = new Config([]);
    $this->client = mock(ClientInterface::class)->expect();
    $this->packer = mock(PackerInterface::class)->expect();
    $this->urlGenerator = mock(UrlGenerator::class)->expect();
    $this->app = new App($config, $this->client, $this->packer, $this->urlGenerator);
});
