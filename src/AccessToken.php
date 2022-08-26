<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduSdk\Youdu;

use Exception;
use YouduSdk\Youdu\Crypt\Prpcrypt;
use YouduSdk\Youdu\Exceptions\ErrorCode;
use YouduSdk\Youdu\Http\ClientInterface;

class AccessToken
{
    public function __construct(protected ClientInterface $client, protected Prpcrypt $crypter, protected Config $config)
    {
    }

    /**
     * Get access token.
     */
    public function get(): string
    {
        $encrypted = $this->encryptMsg((string) time());

        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $encrypted,
        ];

        $resp = $this->client->post('/cgi/gettoken', $parameters);
        $body = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($body['errcode'] != 0) {
            throw new Exception($body['errmsg'], $body['errcode']);
        }

        $decrypted = $this->decryptMsg($body['encrypt']);
        $decoded = json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR);

        return $decoded['accessToken'] ?? '';
    }

    /**
     * 加密.
     */
    public function encryptMsg(string $unencrypted = ''): string
    {
        [$errcode, $encrypted] = $this->crypter->encrypt($unencrypted, $this->config->getAppId());

        if ($errcode != 0) {
            throw new Exception($encrypted, $errcode);
        }

        return $encrypted;
    }

    /**
     * 解密.
     */
    public function decryptMsg(?string $encrypted): string
    {
        if (strlen($this->config->getAesKey()) != 44) {
            throw new Exception('Illegal aesKey', ErrorCode::$IllegalAesKey);
        }

        [$errcode, $decrypted] = $this->crypter->decrypt($encrypted, $this->config->getAppId());

        if ($errcode != 0) {
            throw new Exception('Decrypt failed:' . $decrypted, (int) $errcode);
        }

        return $decrypted;
    }
}
