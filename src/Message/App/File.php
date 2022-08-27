<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Message\App;

class File extends AbstractMessage
{
    /**
     * 文件消息.
     *
     * @param string $mediaId 消息内容，支持表情，最长不超过600个字符，超出部分将自动截取
     */
    public function __construct(protected string $mediaId = '')
    {
    }

    /**
     * 转成 array.
     * @return array
     */
    public function toArray()
    {
        return [
            'toUser' => $this->toUser,
            'toDept' => $this->toDept,
            'msgType' => 'file',
            'file' => [
                'media_id' => $this->mediaId,
            ],
        ];
    }
}
