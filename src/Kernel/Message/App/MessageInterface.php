<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */

namespace YouduPhp\Youdu\Kernel\Message\App;

use JsonSerializable;

interface MessageInterface extends JsonSerializable
{
    public function toUser(string $toUser): self;

    public function toDept(string $toDept): self;

    public function toArray(): array;

    public function toJson($options = 0);
}
