<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Generator;

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
