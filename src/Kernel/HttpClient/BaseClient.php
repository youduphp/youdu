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
use YouduPhp\Youdu\Kernel\Config;
use YouduPhp\Youdu\Kernel\Exception\AccessTokenDoesNotExistException;
use YouduPhp\Youdu\Kernel\Util\Packer\Packer;
use YouduPhp\Youdu\Kernel\Util\Packer\PackerInterface;

class BaseClient
{
    protected PackerInterface $packer;

    public function __construct(protected Config $config, protected ClientInterface $client, protected ?CacheInterface $cache = null)
    {
        $this->packer = new Packer($config);
    }

    public function httpGet(string $uri, array $query = []): Response
    {
        return $this->buildResponse(
            $this->client->request('GET', $uri, [
                'query' => $this->preformatQuery($query),
            ])
        );
    }

    public function httpPost(string $uri, array $data = []): Response
    {
        return $this->buildResponse(
            $this->client->request('POST', $uri, [
                'json' => $this->preformatParams($data),
                'query' => $this->preformatQuery(),
            ])
        );
    }

    public function httpUpload(string $uri, string $file, array $data = []): Response
    {
        return $this->buildResponse(
            $this->client->request('POST', $uri, [
                'multipart' => $this->preformatUploadFileParams($file, $data),
                'query' => $this->preformatQuery(),
            ])
        );
    }

    public function preformatUploadFileParams(string $file, array $params = []): array
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
        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $this->packer->pack((string) time()),
        ];

        $cacheKey = sprintf(
            '%s:%s:access_token',
            $this->config->getBuin(),
            $this->config->getAppId()
        );

        if ($accessToken = $this->cache?->get($cacheKey)) {
            return $accessToken;
        }

        $response = $this->buildResponse(
            $this->client->request('POST', '/cgi/gettoken', ['json' => $parameters])
        )->throw();

        if (! $response->json('accessToken', '')) {
            throw new AccessTokenDoesNotExistException('Get access token failed.');
        }

        $accessToken = $response->json('accessToken', '');

        $this->cache?->set($cacheKey, $accessToken, (int) ($response->json('expireIn', 7200) - 60));

        return $accessToken;
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
}
