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

abstract class AbstractMessage implements MessageInterface
{
    protected ?string $sender = null;

    protected ?string $receiver = null;

    protected ?string $sessionId = null;

    public function sender(string $sender)
    {
        $this->sender = $sender;
    }

    public function receiver(string $receiver)
    {
        $this->receiver = $receiver;
    }

    public function session(string $sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function toJson($options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function jsonSerialize()
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
