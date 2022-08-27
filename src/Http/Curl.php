<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Http;

use CURLFile;
use YouduPhp\Youdu\Exception\Http\RequestException;

class Curl implements ClientInterface
{
    protected string $baseUri;

    protected string $userAgent;

    protected int|float $timeout = 5;

    /**
     * construct.
     */
    public function __construct(array $options = [])
    {
        $this->baseUri = trim($options['base_uri'] ?? '', '/');
        $this->timeout = $options['timeout'] ?? 5;
        $this->userAgent = 'Youdu/2.0';
    }

    /**
     * GET.
     *
     * @param mixed $query
     * @throws RequestException
     */
    public function get(string $uri = '', $query = null): array
    {
        if (! empty($query)) {
            $uri .= (str_contains($uri, '?') ? '&' : '&') . http_build_query($query);
        }

        $uri = $this->baseUri . $uri;

        $options = [
            CURLOPT_URL => $uri,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => $this->timeout,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if ($errno = curl_errno($ch)) {
            throw new RequestException('Curl Request Error: ' . curl_error($ch), $errno);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        curl_close($ch);

        return [
            'header' => $header,
            'body' => $body,
            'httpCode' => $httpCode,
        ];
    }

    /**
     * POST.
     *
     * @param mixed $formParams
     * @throws RequestException
     */
    public function post(string $uri, $formParams = null): array
    {
        $uri = $this->baseUri . $uri;

        $options = [
            CURLOPT_URL => $uri,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'content-Length: ' . strlen(json_encode($formParams, JSON_THROW_ON_ERROR)),
            ],
            CURLOPT_POSTFIELDS => json_encode($formParams, JSON_THROW_ON_ERROR),
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => $this->timeout,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if ($errno = curl_errno($ch)) {
            throw new RequestException('Curl Request Error: ' . curl_error($ch), $errno);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        curl_close($ch);

        return [
            'header' => $header,
            'body' => $body,
            'httpCode' => $httpCode,
        ];
    }

    /**
     * Upload.
     *
     * @param mixed $formParams
     * @throws RequestException
     */
    public function upload(string $uri, $formParams = null): array
    {
        $uri = $this->baseUri . $uri;

        $options = [
            CURLOPT_URL => $uri,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $formParams,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => $this->timeout,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if ($errno = curl_errno($ch)) {
            throw new RequestException('Curl Request Error: ' . curl_error($ch), $errno);
        }

        curl_close($ch);

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Make a upload file.
     */
    public function makeUploadFile(string $file): CURLFile
    {
        $mime = mime_content_type($file);
        $info = pathinfo($file);
        $name = $info['basename'];

        return new CURLFile($file, $mime, $name);
    }
}
