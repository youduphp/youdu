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

use YouduPhp\Youdu\Kernel\HttpClient\BaseClient;

class Client extends BaseClient
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
    public function create(string $name): int|string
    {
        $parameters = [
            'name' => $name,
        ];

        return $this->httpPost('/cgi/group/create', $parameters)->json('id');
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

        $this->httpPost('/cgi/group/update', $parameters)->throw();

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
     */
    public function addMember(string $groupId, array $members = []): bool
    {
        $parameters = [
            'id' => $groupId,
            'userList' => $members,
        ];

        $this->httpPost('/cgi/group/addmember', $parameters)->throw();

        return true;
    }

    /**
     * 删除群成员.
     */
    public function delMember(string $groupId, array $members = []): bool
    {
        $parameters = [
            'id' => $groupId,
            'userList' => $members,
        ];

        $this->httpPost('/cgi/group/delmember', $parameters)->throw();

        return true;
    }

    /**
     * 查询用户是否是群成员.
     * @param int|string $userId
     */
    public function isMember(string $groupId, $userId): bool
    {
        return $this->httpGet('/cgi/group/ismember', ['id' => $groupId, 'userId' => $userId])->json('belong') ? true : false;
    }
}