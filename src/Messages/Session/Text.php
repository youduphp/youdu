<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Messages\Session;

class Text extends AbstractMessage
{
    /**
     * 文本消息.
     *
     * @param string $content 消息内容，支持表情
     */
    public function __construct(protected string $content = '')
    {
    }

    public function toArray(): array
    {
        return [
            'sessionId' => $this->sessionId,
            'receiver' => $this->receiver,
            'sender' => $this->sender,
            'msgType' => 'text',
            'text' => [
                'content' => $this->content,
            ],
        ];
    }
}
