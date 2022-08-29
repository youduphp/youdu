<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu;

use GuzzleHttp\ClientInterface;
use YouduPhp\Youdu\Kernel\Config;
use YouduPhp\Youdu\Kernel\Exception\InvalidArgumentException;

/**
 * @property \YouduPhp\Youdu\Kernel\Dept\Client $dept
 * @property \YouduPhp\Youdu\Kernel\Message\Client $message
 * @property \YouduPhp\Youdu\Kernel\User\Client $user
 * @property \YouduPhp\Youdu\Kernel\Session\Client $session
 * @property \YouduPhp\Youdu\Kernel\Media\Client $media
 * @property \YouduPhp\Youdu\Kernel\Group\Client $group
 */
class Application
{
    private array $container = [];

    public function __construct(protected Config $config, private ClientInterface $client)
    {
    }

    public function __get($name)
    {
        if (isset($this->container[$name])) {
            return $this->container[$name];
        }

        $class = __NAMESPACE__ . '\\Kernel\\' . ucfirst($name) . '\\Client';

        if (! class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class "%s" not found', $class));
        }

        return $this->container[$name] = new $class($this->client, $this->config);
    }
}
