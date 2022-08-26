<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduSdk\Youdu\Messages\Session;

use JsonSerializable;

interface MessageInterface extends JsonSerializable
{
    public function sender(string $sender);

    public function receiver(string $receiver);

    public function session(string $sessionId);

    public function toArray(): array;

    public function toJson($options = 0): string;
}
