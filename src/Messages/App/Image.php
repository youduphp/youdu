<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Messages\App;

class Image extends AbstractMessage
{
    /**
     * 图片消息.
     *
     * @param string $mediaId 图片素材文件ID。通过上传素材文件接口获取
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
            'msgType' => 'image',
            'image' => [
                'media_id' => $this->mediaId,
            ],
        ];
    }
}
