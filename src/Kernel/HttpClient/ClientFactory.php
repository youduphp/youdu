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

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class ClientFactory implements ClientFactoryInterface
{
    protected array $options = [];

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function create(array $options = []): ClientInterface
    {
        return new Client(array_merge($this->options, $options));
    }
}
