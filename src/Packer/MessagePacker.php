<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Packer;

use YouduPhp\Youdu\Config;
use YouduPhp\Youdu\Encipher\Prpcrypt;
use YouduPhp\Youdu\Exception\ErrorCode;
use YouduPhp\Youdu\Exception\Exception;

class MessagePacker implements PackerInterface
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
