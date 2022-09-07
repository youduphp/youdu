<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Kernel\Group;

use YouduPhp\Youdu\Kernel\HttpClient\AbstractClient;

class Client extends AbstractClient
{
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

        return $this->httpGet('/cgi/group/list', $parameters)->throw()->json('groupList', []);
    }

    /**
     * 创建群.
     */
    public function create(string $name): string
    {
        return (string) $this->httpPostJson('/cgi/group/create', ['name' => $name])->throw()->json('id');
    }

    /**
     * 删除群.
     */
    public function delete(string $groupId): bool
    {
        $this->httpGet('/cgi/group/delete', ['groupId' => $groupId])->throw();

        return true;
    }

    /**
     * 修改群名称.
     */
    public function update(string $groupId, string $name): bool
    {
        $parameters = [
            'id' => $groupId,
            'name' => $name,
        ];

        $this->httpPostJson('/cgi/group/update', $parameters)->throw();

        return true;
    }

    /**
     * 查看群信息.
     */
    public function info(string $groupId): array
    {
        return $this->httpGet('/cgi/group/info', ['id' => $groupId])->throw()->json();
    }

    /**
     * 添加群成员.
     * @param int[]|string[] $members
     */
    public function addMember(string $groupId, array $members = []): bool
    {
        $parameters = [
            'id' => $groupId,
            'userList' => array_map(fn ($member) => (string) $member, $members),
        ];

        $this->httpPostJson('/cgi/group/addmember', $parameters)->throw();

        return true;
    }

    /**
     * 删除群成员.
     * @param int[]|string[] $members
     */
    public function delMember(string $groupId, array $members = []): bool
    {
        $parameters = [
            'id' => $groupId,
            'userList' => array_map(fn ($member) => (string) $member, $members),
        ];

        $this->httpPostJson('/cgi/group/delmember', $parameters)->throw();

        return true;
    }

    /**
     * 查询用户是否是群成员.
     * @param int|string $userId
     */
    public function isMember(string $groupId, $userId): bool
    {
        $parameters = [
            'id' => $groupId,
            'userId' => (string) $userId,
        ];

        return $this->httpGet('/cgi/group/ismember', $parameters)->json('belong') ? true : false;
    }
}
