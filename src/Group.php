<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu;

use YouduPhp\Youdu\Exception\ErrorCode;
use YouduPhp\Youdu\Exception\Exception;
use YouduPhp\Youdu\Generator\UrlGenerator;
use YouduPhp\Youdu\Http\ClientInterface;
use YouduPhp\Youdu\Packer\PackerInterface;

class Group
{
    public function __construct(protected Config $config, protected ClientInterface $client, protected PackerInterface $packer, protected UrlGenerator $urlGenerator)
    {
    }

    /**
     * 获取群列表.
     * @param int|string $userId
     */
    public function lists($userId = ''): array
    {
        $parameters = [];

        if ($userId) {
            $parameters['userId'] = $userId;
        }

        $resp = $this->client->get($this->urlGenerator->generate('/cgi/group/list'), $parameters);
        $decoded = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($decoded['errcode'] !== ErrorCode::$OK) {
            throw new Exception($decoded['errmsg'], 1);
        }

        $decrypted = $this->packer->unpack($decoded['encrypt'] ?? '');

        return json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR)['groupList'] ?? [];
    }

    /**
     * 创建群.
     */
    public function create(string $name): int|string
    {
        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $this->packer->pack(json_encode([
                'name' => $name,
            ], JSON_THROW_ON_ERROR)),
        ];

        $resp = $this->client->post($this->urlGenerator->generate('/cgi/group/create'), $parameters);

        if ($resp['httpCode'] != 200) {
            throw new Exception('http request code ' . $resp['httpCode'], ErrorCode::$IllegalHttpReq);
        }

        $body = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($body['errcode'] !== ErrorCode::$OK) {
            throw new Exception($body['errmsg'], $body['errcode']);
        }

        $decrypted = $this->packer->unpack($body['encrypt']);
        $decoded = json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR);

        return $decoded['id'];
    }

    /**
     * 删除群.
     */
    public function delete(string $groupId): bool
    {
        $resp = $this->client->get($this->urlGenerator->generate('/cgi/group/delete'), ['groupId' => $groupId]);
        $decoded = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($decoded['errcode'] !== ErrorCode::$OK) {
            throw new Exception($decoded['errmsg'], 1);
        }

        return true;
    }

    /**
     * 修改群名称.
     */
    public function update(string $groupId, string $name): bool
    {
        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $this->packer->pack(json_encode([
                'id' => $groupId,
                'name' => $name,
            ], JSON_THROW_ON_ERROR)),
        ];

        $resp = $this->client->post($this->urlGenerator->generate('/cgi/group/update'), $parameters);

        if ($resp['httpCode'] != 200) {
            throw new Exception('http request code ' . $resp['httpCode'], ErrorCode::$IllegalHttpReq);
        }

        $body = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($body['errcode'] !== ErrorCode::$OK) {
            throw new Exception($body['errmsg'], $body['errcode']);
        }

        return true;
    }

    /**
     * 查看群信息.
     */
    public function info(string $groupId): array
    {
        $resp = $this->client->get($this->urlGenerator->generate('/cgi/group/info'), ['id' => $groupId]);
        $decoded = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($decoded['errcode'] !== ErrorCode::$OK) {
            throw new Exception($decoded['errmsg'], 1);
        }

        $decrypted = $this->packer->unpack($decoded['encrypt'] ?? '');

        return json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR) ?? [];
    }

    /**
     * 添加群成员.
     */
    public function addMember(string $groupId, array $members = []): bool
    {
        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $this->packer->pack(json_encode([
                'id' => $groupId,
                'userList' => $members,
            ], JSON_THROW_ON_ERROR)),
        ];

        $resp = $this->client->post($this->urlGenerator->generate('/cgi/group/addmember'), $parameters);

        if ($resp['httpCode'] != 200) {
            throw new Exception('http request code ' . $resp['httpCode'], ErrorCode::$IllegalHttpReq);
        }

        $body = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($body['errcode'] !== ErrorCode::$OK) {
            throw new Exception($body['errmsg'], $body['errcode']);
        }

        return true;
    }

    /**
     * 删除群成员.
     */
    public function delMember(string $groupId, array $members = []): bool
    {
        $parameters = [
            'encrypt' => $this->packer->pack(json_encode([
                'id' => $groupId,
                'userList' => $members,
            ], JSON_THROW_ON_ERROR)),
        ];

        $resp = $this->client->post($this->urlGenerator->generate('/cgi/group/delmember'), $parameters);

        if ($resp['httpCode'] != 200) {
            throw new Exception('http request code ' . $resp['httpCode'], ErrorCode::$IllegalHttpReq);
        }

        $body = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($body['errcode'] !== ErrorCode::$OK) {
            throw new Exception($body['errmsg'], $body['errcode']);
        }

        return true;
    }

    /**
     * 查询用户是否是群成员.
     * @param int|string $userId
     */
    public function isMember(string $groupId, $userId): bool
    {
        $resp = $this->client->get($this->urlGenerator->generate('/cgi/group/ismember'), ['id' => $groupId, 'userId' => $userId]);
        $decoded = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($decoded['errcode'] !== ErrorCode::$OK) {
            throw new Exception($decoded['errmsg'], 1);
        }

        $decrypted = $this->packer->unpack($decoded['encrypt'] ?? '');

        return json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR)['belong'] ?? false;
    }
}
