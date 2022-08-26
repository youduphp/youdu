<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduSdk\Youdu\Generator;

use YouduSdk\Youdu\Config;

class UrlGenerator
{
    protected AccessTokenGenerator $accessTokenGenerator;

    public function __construct(Config $config)
    {
        $this->accessTokenGenerator = $config->getAccessTokenGenerator();
    }

    public function generate(string $url, bool $appendToken = true): string
    {
        if (! $appendToken) {
            return $url;
        }

        return sprintf(
            '%s%saccessToken=%s',
            $url,
            strpos($url, '?') == false ? '?' : '&',
            $this->accessTokenGenerator->generate()
        );
    }
}
