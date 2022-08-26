<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduSdk\Youdu\Messages\App;

class Mpnews extends AbstractMessage
{
    /**
     * 图文消息.
     *
     * @param Items\Mpnews $mpnews 消息内容，支持表情，最长不超过600个字符，超出部分将自动截取
     */
    public function __construct(protected Items\Mpnews $mpnews)
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
            'msgType' => 'mpnews',
            'mpnews' => $this->mpnews->toArray(),
        ];
    }
}
