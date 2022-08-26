<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduSdk\Youdu\Messages\App\Items;

class Item implements MessageItemInterface
{
    protected array $items = [];

    /**
     * 转成 array.
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * 转成 json.
     * @param mixed $options
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->items, $options);
    }
}
