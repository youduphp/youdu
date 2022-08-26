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

class UrlGenerator
{
    public function __construct(protected AccessTokenGenerator $accessTokenGenerator)
    {
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
