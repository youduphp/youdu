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

use Closure;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;

class Guzzle implements ClientInterface
{
    protected GuzzleClientInterface $client;

    public function __construct(Closure $clientFactory)
    {
        $this->client = $clientFactory();
    }

    /**
     * get.
     * @param mixed $query
     */
    public function get(string $uri, $query = null): array
    {
        $response = $this->client->request('GET', $uri, ['query' => $query]);

        return [
            'header' => $response->getHeaders(),
            'body' => $response->getBody()->getContents(),
            'httpCode' => $response->getStatusCode(),
        ];
    }

    /**
     * post.
     * @param mixed $formParams
     */
    public function post(string $uri, $formParams = null): array
    {
        $response = $this->client->request('POST', $uri, [
            'json' => $formParams,
        ]);

        return [
            'header' => $response->getHeaders(),
            'body' => $response->getBody()->getContents(),
            'httpCode' => $response->getStatusCode(),
        ];
    }

    /**
     * upload.
     * @param mixed $formParams
     */
    public function upload(string $uri, $formParams = null): array
    {
        $parts = [];

        foreach ((array) $formParams as $key => $value) {
            $parts[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }
        $formParams = $parts;
        $response = $this->client->request('POST', $uri, [
            'multipart' => $formParams,
        ]);

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Make a upload file.
     *
     * @return false|resource
     */
    public function makeUploadFile(string $file)
    {
        return fopen($file, 'r');
    }
}
