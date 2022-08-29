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
use YouduPhp\Youdu\Kernel\Util\Packer\PackerInterface;

use function YouduPhp\Youdu\Kernel\Util\tap;

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
            ])
        );
    }

    protected function httpPost(string $uri, array $data = []): Response
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
        return $this->buildResponse(
            $this->client->request('POST', $uri, [
                'multipart' => $this->preformatUploadFileParams($file, $data),
                'query' => $this->preformatQuery(),
            ])
        );
    }

    protected function preformatUploadFileParams(string $file, array $params = []): array
    {
        $array = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'file' => $this->makeUploadFile(realpath($file)),
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

    protected function buildResponse(ResponseInterface $response): Response
    {
        return new Response($response, $this->packer);
    }

    /**
     * Make a upload file.
     *
     * @return false|resource
     */
    protected function makeUploadFile(string $file)
    {
        return fopen($file, 'r');
    }

    protected function fileGetContents(string $file, bool $pack = true): string
    {
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

        return $pack ? $this->packer->pack($contents) : $contents;
    }
}
