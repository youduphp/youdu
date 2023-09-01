<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu;

use Psr\SimpleCache\CacheInterface;
use YouduPhp\Youdu\Kernel\Exception\InvalidArgumentException;
use YouduPhp\Youdu\Kernel\HttpClient\ClientFactory;
use YouduPhp\Youdu\Kernel\HttpClient\ClientFactoryInterface;
use YouduPhp\Youdu\Kernel\Utils\Packer\Packer;
use YouduPhp\Youdu\Kernel\Utils\Packer\PackerInterface;

/**
 * @method Kernel\Dept\Client dept()
 * @method Kernel\Message\Client message()
 * @method Kernel\User\Client user()
 * @method Kernel\Session\Client session()
 * @method Kernel\Media\Client media()
 * @method Kernel\Group\Client group()
 */
class Application
{
    private array $container = [];

    private PackerInterface $packer;

    public function __construct(protected Config $config, private ?ClientFactoryInterface $clientFactory = null, protected ?CacheInterface $cache = null)
    {
        if (! $this->clientFactory) {
            $clientFactory = new ClientFactory();
            $clientFactory->setOptions([
                'base_uri' => $config->getApi(),
                'timeout' => $config->getTimeout(),
            ]);
            $this->clientFactory = $clientFactory;
        }
        $this->packer = new Packer($config);
    }

    public function __call($name, $arguments)
    {
        if (isset($this->container[$name])) {
            return $this->container[$name];
        }

        $class = __NAMESPACE__ . '\\Kernel\\' . ucfirst($name) . '\\Client';

        if (! class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class "%s" not found', $class));
        }

        return $this->container[$name] = new $class(
            $this->config,
            $this->clientFactory,
            $this->packer,
            $this->cache
        );
    }
}
