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

class Voice extends AbstractMessage
{
    /**
     * 语音消息.
     *
     * @param string $mediaId 语音素材文件id。通过上传素材文件接口获取
     */
    public function __construct(protected string $mediaId = '')
    {
    }

    public function toArray(): array
    {
        return [
            'sessionId' => $this->sessionId,
            'receiver' => $this->receiver,
            'sender' => $this->sender,
            'msgType' => 'voice',
            'voice' => [
                'media_id' => $this->mediaId,
            ],
        ];
    }
}
