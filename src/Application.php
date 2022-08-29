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

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\SimpleCache\CacheInterface;
use YouduPhp\Youdu\Kernel\Config;
use YouduPhp\Youdu\Kernel\Exception\InvalidArgumentException;

/**
 * @property Kernel\Dept\Client $dept
 * @property Kernel\Message\Client $message
 * @property Kernel\User\Client $user
 * @property Kernel\Session\Client $session
 * @property Kernel\Media\Client $media
 * @property Kernel\Group\Client $group
 */
class Application
{
    private array $container = [];

    public function __construct(protected Config $config, private ?ClientInterface $client = null, protected ?CacheInterface $cache = null)
    {
        $this->client = $client ?? new Client([
            'base_uri' => $config->getApi(),
            'timeout' => $config->getTimeout(),
        ]);
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

        return $this->container[$name] = new $class($this->config, $this->client, $this->cache);
    }
}
