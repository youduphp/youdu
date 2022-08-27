<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
use YouduSdk\Youdu\App;
use YouduSdk\Youdu\Config;
use YouduSdk\Youdu\Dept;
use YouduSdk\Youdu\Group;
use YouduSdk\Youdu\Http\Curl;
use YouduSdk\Youdu\Media;
use YouduSdk\Youdu\Session;
use YouduSdk\Youdu\User;

beforeEach(function () {
    $this->app = new App(new Config([]), new Curl(['base_uri' => '', 'timeout' => 5.0]));
});

test('Test App getter', function () {
    expect($this->app->dept())->toBeInstanceOf(Dept::class);
    expect($this->app->group())->toBeInstanceOf(Group::class);
    expect($this->app->media())->toBeInstanceOf(Media::class);
    expect($this->app->session())->toBeInstanceOf(Session::class);
    expect($this->app->user())->toBeInstanceOf(User::class);
});
