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

class File extends AbstractMessage
{
    /**
     * 文件消息.
     *
     * @param string $mediaId 素材文件id。通过上传素材文件接口获取
     * @param string $name 文件名
     * @param int $size 文件大小
     */
    public function __construct(protected string $mediaId = '', protected string $name = '', protected int $size = 0)
    {
    }

    public function toArray(): array
    {
        return [
            'sessionId' => $this->sessionId,
            'receiver' => $this->receiver,
            'sender' => $this->sender,
            'msgType' => 'file',
            'file' => [
                'media_id' => $this->mediaId,
                'name' => $this->name,
                'size' => $this->size,
            ],
        ];
    }
}
