<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */

namespace YouduPhp\Youdu\Kernel\Message\Session;

use function YouduPhp\Youdu\Kernel\Utils\tap;

abstract class AbstractMessage implements MessageInterface
{
    protected ?string $sender = null;

    protected ?string $receiver = null;

    protected ?string $sessionId = null;

    public function sender(string $sender): self
    {
        return tap($this, fn () => $this->sender = $sender);
    }

    public function receiver(string $receiver): self
    {
        return tap($this, fn () => $this->receiver = $receiver);
    }

    public function session(string $sessionId): self
    {
        return tap($this, fn () => $this->sessionId = $sessionId);
    }

    public function toJson($options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function jsonSerialize(): array
    {
        $data = $this->toArray();

        if (is_null($this->receiver) && isset($data['receiver'])) {
            unset($data['receiver']);
        }

        if (is_null($this->sessionId) && isset($data['sessionId'])) {
            unset($data['sessionId']);
        }

        return $data;
    }
}
