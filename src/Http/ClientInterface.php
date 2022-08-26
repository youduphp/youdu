<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduSdk\Youdu\Http;

interface ClientInterface
{
    public function __construct(string $baseUri = '', int $timeout = 2);

    public function get(string $uri, $query = null): array;

    public function post(string $uri, $formParams = null): array;

    public function upload(string $uri, $formParams = null): array;

    public function makeUploadFile(string $file);
}
