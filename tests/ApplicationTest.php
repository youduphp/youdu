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
use YouduPhp\Youdu\Kernel\HttpClient\ClientFactoryInterface;

beforeEach(function () {
    $client = mock(ClientFactoryInterface::class);
    $config = makeConfig();
    $this->application = new Application($config, $client);
});

it('assert application getter', function () {
    expect($this->application->dept())->toBeInstanceOf(YouduPhp\Youdu\Kernel\Dept\Client::class);
    expect($this->application->group())->toBeInstanceOf(YouduPhp\Youdu\Kernel\Group\Client::class);
    expect($this->application->media())->toBeInstanceOf(YouduPhp\Youdu\Kernel\Media\Client::class);
    expect($this->application->message())->toBeInstanceOf(YouduPhp\Youdu\Kernel\Message\Client::class);
    expect($this->application->session())->toBeInstanceOf(YouduPhp\Youdu\Kernel\Session\Client::class);
    expect($this->application->user())->toBeInstanceOf(YouduPhp\Youdu\Kernel\User\Client::class);
});
