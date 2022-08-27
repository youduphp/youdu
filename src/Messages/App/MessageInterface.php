<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Messages\App;

use JsonSerializable;

interface MessageInterface extends JsonSerializable
{
    public function toUser(string $toUser);

    public function toDept(string $toDept);

    public function toArray();

    public function toJson($options = 0);
}
