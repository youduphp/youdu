<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Kernel\Util\Packer;

use YouduPhp\Youdu\Kernel\Config;
use YouduPhp\Youdu\Kernel\Exception\ErrorCode;
use YouduPhp\Youdu\Kernel\Exception\Exception;
use YouduPhp\Youdu\Kernel\Util\Encipher\Prpcrypt;

class Packer implements PackerInterface
{
    protected Prpcrypt $crypter;

    public function __construct(protected Config $config)
    {
        $this->crypter = new Prpcrypt($config->getAesKey());
    }

    public function pack(string $string): string
    {
        [$errcode, $encrypted] = $this->crypter->encrypt($string, $this->config->getAppId());

        if ($errcode != ErrorCode::$OK) {
            throw new Exception($encrypted, (int) $errcode);
        }

        return $encrypted;
    }

    public function unpack(string $string): string
    {
        if (strlen($this->config->getAesKey()) != 44) {
            throw new Exception('Illegal aesKey', ErrorCode::$IllegalAesKey);
        }

        [$errcode, $decrypted] = $this->crypter->decrypt($string, $this->config->getAppId());

        if ($errcode != ErrorCode::$OK) {
            throw new Exception('Decrypt failed:' . $decrypted, (int) $errcode);
        }

        return $decrypted;
    }
}
