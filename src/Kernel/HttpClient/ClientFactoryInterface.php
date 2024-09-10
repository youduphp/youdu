<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */

namespace YouduPhp\Youdu\Kernel\HttpClient;

use GuzzleHttp\ClientInterface;

interface ClientFactoryInterface
{
    public function setOptions(array $options): void;

    public function create(array $options = []): ClientInterface;
}
