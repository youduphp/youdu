<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Message\Session;

use JsonSerializable;

interface MessageInterface extends JsonSerializable
{
    public function sender(string $sender): self;

    public function receiver(string $receiver): self;

    public function session(string $sessionId): self;

    public function toArray(): array;

    public function toJson($options = 0): string;
}
