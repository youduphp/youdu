<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Messages\App;

class Sms extends AbstractMessage
{
    /**
     * 图片消息.
     *
     * @param string $from 发送短信的手机号码
     * @param string $content 消息内容，支持表情，最长不超过600个字符，超出部分将自动截取
     */
    public function __construct(protected string $from = '', protected string $content = '')
    {
    }

    /**
     * 转成 array.
     * @return (string|array)[]
     */
    public function toArray()
    {
        return [
            'toUser' => $this->toUser,
            'toDept' => $this->toDept,
            'msgType' => 'sms',
            'sms' => [
                'from' => $this->from,
                'content' => $this->content,
            ],
        ];
    }
}
