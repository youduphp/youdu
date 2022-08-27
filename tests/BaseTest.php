<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduPhp\Youdu\App;
use YouduPhp\Youdu\Config;
use YouduPhp\Youdu\Dept;
use YouduPhp\Youdu\Group;
use YouduPhp\Youdu\Http\ClientInterface;
use YouduPhp\Youdu\Media;
use YouduPhp\Youdu\Session;
use YouduPhp\Youdu\User;

beforeEach(function () {
    $config = new Config([], mock(ClientInterface::class)->expect());
    $this->app = new App($config);
});

test('Test App getter', function () {
    expect($this->app->dept())->toBeInstanceOf(Dept::class);
    expect($this->app->group())->toBeInstanceOf(Group::class);
    expect($this->app->media())->toBeInstanceOf(Media::class);
    expect($this->app->session())->toBeInstanceOf(Session::class);
    expect($this->app->user())->toBeInstanceOf(User::class);
});
