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
use RuntimeException;
use YouduPhp\Youdu\Kernel\Config;
use YouduPhp\Youdu\Kernel\Exception\AccessTokenDoesNotExistException;
use YouduPhp\Youdu\Kernel\Util\Packer\Packer;
use YouduPhp\Youdu\Kernel\Util\Packer\PackerInterface;

class BaseClient
{
    protected PackerInterface $packer;

    public function __construct(protected ClientInterface $client, protected Config $config)
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
                'form_params' => $this->preformatParams($data),
                'query' => $this->preformatQuery(),
            ])
        );
    }

    public function httpUpload(string $uri, array $data = []): Response
    {
        return $this->buildResponse(
            $this->client->request('POST', $uri, [
                'multipart' => $this->preformatParams($data),
                'query' => $this->preformatQuery(),
            ])
        );
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

        $response = $this->buildResponse(
            $this->client->request('POST', '/cgi/gettoken', $parameters)
        );

        if ($response->json('errcode') != 0) {
            throw new RuntimeException($response->json('errmsg'), $response->json('errcode'));
        }

        $decrypted = $this->packer->unpack($response->json('encrypt'));
        $decoded = json_decode($decrypted, true);

        if (empty($decoded['accessToken'])) {
            throw new AccessTokenDoesNotExistException('Get access token failed.');
        }

        return $decoded['accessToken'];
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
