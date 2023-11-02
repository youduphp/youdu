<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Kernel\Dept;

use YouduPhp\Youdu\Kernel\HttpClient\AbstractClient;

class Client extends AbstractClient
{
    /**
     * 获取部门列表.
     */
    public function lists(int $parentDeptId = 0): array
    {
        return $this->httpGet('/cgi/dept/list', ['id' => $parentDeptId])->throw()->json('deptList', []);
    }

    /**
     * 创建部门.
     *
     * @param int $deptId 部门id，整型。必须大于0
     * @param string $name 部门名称。不能超过32个字符（包括汉字和英文字母）
     * @param int $parentId 父部门id。根部门id为0
     * @param int $sortId 整型。在父部门中的排序值。值越大排序越靠前。填0自动生成。同级部门不允许重复（推荐全局唯一）
     * @param string $alias 字符串。部门id的别名（通常存放以字符串表示的部门id）。唯一不为空，相同会覆盖旧数据。
     */
    public function create(int $deptId, string $name, int $parentId = 0, $sortId = 0, string $alias = ''): int
    {
        $parameters = [
            'id' => $deptId,
            'name' => $name,
            'parentId' => $parentId,
            'sortId' => $sortId,
            'alias' => $alias,
        ];

        return (int) $this->httpPostJson('/cgi/dept/create', $parameters)->json('id');
    }

    /**
     * 更新部门.
     *
     * @param int $deptId 部门id，整型。必须大于0
     * @param string $name 部门名称。不能超过32个字符（包括汉字和英文字母）
     * @param int $parentId 父部门id。根部门id为0
     * @param int $sortId 整型。在父部门中的排序值。值越大排序越靠前。填0自动生成。同级部门不允许重复（推荐全局唯一）
     * @param string $alias 字符串。部门id的别名（通常存放以字符串表示的部门id）。唯一不为空，相同会覆盖旧数据。
     */
    public function update(int $deptId, string $name, int $parentId = 0, $sortId = 0, string $alias = ''): bool
    {
        $parameters = [
            'id' => $deptId,
            'name' => $name,
            'parentId' => $parentId,
            'sortId' => $sortId,
            'alias' => $alias,
        ];

        $this->httpPostJson('/cgi/dept/update', $parameters)->throw();

        return true;
    }

    /**
     * 更新部门.
     *
     * @param int $deptId 部门id，整型。必须大于0
     */
    public function delete(int $deptId): bool
    {
        $this->httpGet('/cgi/dept/delete', ['id' => $deptId])->throw();

        return true;
    }

    /**
     * 获取部门ID.
     *
     * @param string $alias 部门alias。携带时查询该alias对应的部门id。不带alias参数时查询全部映射关系。
     * @return array|int 带alias { "id":5 } | 不带alias {"aliasList":[{ "id":1, "alias":"we3pj6cv" }]}
     */
    public function getId(string $alias = ''): array|int
    {
        if ($alias) {
            return (int) $this->httpGet('/cgi/dept/getid', ['alias' => $alias])->json('id', 0);
        }

        return $this->httpGet('/cgi/dept/getid')->json('aliasList', []);
    }
}
