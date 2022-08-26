<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduSdk\Youdu\Packer;

use YouduSdk\Youdu\Config;
use YouduSdk\Youdu\Encipher\Prpcrypt;
use YouduSdk\Youdu\Exceptions\ErrorCode;
use YouduSdk\Youdu\Exceptions\Exception;

class MessagePacker implements PackerInterface
{
    protected Prpcrypt $crypter;

    public function __construct(protected Config $config)
    {
        $this->crypter = new Prpcrypt($config->getAppId(), $config->getAesKey());
    }

    public function pack(string $string): string
    {
        [$errcode, $encrypted] = $this->crypter->encrypt($string);

        if ($errcode != 0) {
            throw new Exception($encrypted, (int) $errcode);
        }

        return $encrypted;
    }

    public function unpack(string $string): string
    {
        if (strlen($this->config->getAesKey()) != 44) {
            throw new Exception('Illegal aesKey', ErrorCode::$IllegalAesKey);
        }

        [$errcode, $decrypted] = $this->crypter->decrypt($string);

        if ($errcode != 0) {
            throw new Exception('Decrypt failed:' . $decrypted, (int) $errcode);
        }

        return $decrypted;
    }
}