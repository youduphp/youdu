<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Kernel\HttpClient;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use YouduPhp\Youdu\Config;
use YouduPhp\Youdu\Kernel\Exception\AccessTokenDoesNotExistException;
use YouduPhp\Youdu\Kernel\Exception\LogicException;
use YouduPhp\Youdu\Kernel\Utils\Packer\PackerInterface;

use function YouduPhp\Youdu\Kernel\Utils\tap;

abstract class AbstractClient
{
    public function __construct(
        protected Config $config,
        protected ClientInterface $client,
        protected PackerInterface $packer,
        protected ?CacheInterface $cache = null
    ) {
    }

    protected function httpGet(string $uri, array $query = []): Response
    {
        return $this->buildResponse(
            $this->client->request('GET', $uri, [
                'query' => $this->preformatQuery($query),
                'base_uri' => $this->config->getApi(),
            ])
        );
    }

    protected function httpPost(string $uri, array $data = []): Response
    {
        return $this->buildResponse(
            $this->client->request('POST', $uri, [
                'form_params' => $this->preformatParams($data),
                'query' => $this->preformatQuery(),
            ])
        );
    }

    protected function httpPostJson(string $uri, array $data = []): Response
    {
        return $this->buildResponse(
            $this->client->request('POST', $uri, [
                'json' => $this->preformatParams($data),
                'query' => $this->preformatQuery(),
            ])
        );
    }

    protected function httpUpload(string $uri, string $file, array $data = []): Response
    {
        try {
            // Get the file contents and pack it
            $tmpFile = $this->createTmpFile($file);

            return $this->buildResponse(
                $this->client->request('POST', $uri, [
                    'multipart' => $this->preformatUploadFileParams($tmpFile, $data),
                    'query' => $this->preformatQuery(),
                ])
            );
        } finally {
            if (isset($tmpFile) && is_file($tmpFile)) {
                unlink($tmpFile);
            }
        }
    }

    protected function buildResponse(ResponseInterface $response): Response
    {
        return new Response($response, $this->packer);
    }

    protected function preformatUploadFileParams(string $file, array $params = []): array
    {
        $array = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'file' => fopen(realpath($file), 'r'),
            'encrypt' => $this->packer->pack(json_encode($params)),
        ];

        if (isset($params['userId'])) {
            $array['userId'] = $params['userId'];
        }

        $data = [];

        foreach ($array as $key => $value) {
            $data[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }

        return $data;
    }

    protected function preformatParams(array $params): array
    {
        if (isset($params['app_id'], $params['msg_encrypt'])) {
            return $params;
        }

        return [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $this->packer->pack(json_encode($params)),
        ];
    }

    protected function preformatQuery(array $query = []): array
    {
        $query['accessToken'] = $this->getAccessToken();

        return $query;
    }

    protected function getAccessToken(): string
    {
        $cacheKey = sprintf(
            '%s:%s:access_token',
            $this->config->getBuin(),
            $this->config->getAppId()
        );

        if ($accessToken = $this->cache?->get($cacheKey)) {
            return $accessToken;
        }

        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $this->packer->pack((string) time()),
        ];

        $response = $this->buildResponse(
            $this->client->request('POST', '/cgi/gettoken', ['json' => $parameters])
        )->throw();
        $ttl = (int) ($response->json('expireIn', 7200) - 60);

        return tap(
            $response->json('accessToken', ''),
            function ($accessToken) use ($cacheKey, $ttl) {
                if (! $accessToken) {
                    throw new AccessTokenDoesNotExistException('Get access token failed.');
                }

                $this->cache?->set($cacheKey, $accessToken, $ttl);
            }
        );
    }

    protected function createTmpFile(string $file): string
    {
        return tap(
            $this->config->getTmpPath() . '/' . uniqid('youdu_'),
            function ($tmpFile) use ($file) {
                if (preg_match('/^https?:\/\//i', $file)) { // 远程文件
                    $contextOptions = stream_context_create([
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ]);

                    $contents = file_get_contents($file, false, $contextOptions);
                } else { // 本地文件
                    $contents = file_get_contents($file);
                }

                if ($contents === false) {
                    throw new LogicException(sprintf('Read file %s failed', $file), 1);
                }

                $contents = $this->packer->pack($contents);

                // Save the packed file to tmp path
                if (file_put_contents($tmpFile, $contents) === false) {
                    throw new LogicException(sprintf('Create tmpfile %s failed', $tmpFile), 1);
                }
            }
        );
    }
}
