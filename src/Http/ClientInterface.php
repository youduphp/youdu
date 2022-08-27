<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Http;

interface ClientInterface
{
    public function get(string $uri, $query = null): array;

    public function post(string $uri, $formParams = null): array;

    public function upload(string $uri, $formParams = null): array;

    public function makeUploadFile(string $file);
}
