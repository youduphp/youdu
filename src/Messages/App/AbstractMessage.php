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

abstract class AbstractMessage implements MessageInterface
{
    protected ?string $toUser = null;

    protected ?string $toDept = null;

    /**
     * 发送至用户.
     */
    public function toUser(string $toUser)
    {
        // 兼容用,隔开
        $toUser = strtr($toUser, ',', '|');
        $this->toUser = $toUser;
    }

    /**
     * 发送至部门.
     */
    public function toDept(string $toDept)
    {
        // 兼容用,隔开
        $toDept = strtr($toDept, ',', '|');
        $this->toDept = $toDept;
    }

    /**
     * 转成 json.
     * @param int $options
     * @return false|string
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * json 序列化.
     * @return array
     */
    public function jsonSerialize()
    {
        $data = $this->toArray();

        if (is_null($this->toUser)) {
            unset($data['toUser']);
        }

        if (is_null($this->toDept)) {
            unset($data['toDept']);
        }

        return $data;
    }
}
