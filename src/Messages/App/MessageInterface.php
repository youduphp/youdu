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

use JsonSerializable;

interface MessageInterface extends JsonSerializable
{
    public function toUser(string $toUser);

    public function toDept(string $toDept);

    public function toArray();

    public function toJson($options = 0);
}
