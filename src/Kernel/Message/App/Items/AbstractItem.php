<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */

namespace YouduPhp\Youdu\Kernel\Message\App\Items;

class AbstractItem implements ItemInterface
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
