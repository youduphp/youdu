<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Kernel\User;

use YouduPhp\Youdu\Kernel\HttpClient\AbstractClient;

class Client extends AbstractClient
{
    /**
     * 获取用户列表.
     */
    public function simpleList(?int $deptId = 0): array
    {
        return $this->httpGet('/cgi/user/simplelist', ['deptId' => $deptId])->throw()->json('userList', []);
    }

    /**
     * 获取用户列表.
     */
    public function lists(?int $deptId = 0): array
    {
        return $this->httpGet('/cgi/user/list', ['deptId' => $deptId])->throw()->json('userList', []);
    }

    /**
     * 创建用户.
     *
     * @param int|string $userId 用户id(帐号)，企业内必须唯一。长度为1~64个字符（包括汉字和英文字母）
     * @param string $name 用户名称。长度为0~64个字符（包括汉字和英文字母，可为空）
     * @param int $gender 性别，整型。0表示男性，1表示女性
     * @param string $mobile 手机号码。企业内必须唯一
     * @param string $phone 电话号码
     * @param string $email 邮箱。长度为0~64个字符
     * @param array $dept 所属部门列表,不超过20个
     */
    public function create(int|string $userId, string $name, int $gender = 0, string $mobile = '', string $phone = '', string $email = '', array $dept = []): bool
    {
        $parameters = [
            'userId' => (string) $userId,
            'name' => $name,
            'gender' => $gender,
            'mobile' => $mobile,
            'phone' => $phone,
            'email' => $email,
            'dept' => $dept,
        ];

        $this->httpPostJson('/cgi/user/create', $parameters)->throw();

        return true;
    }

    /**
     * 更新用户.
     *
     * @param int|string $userId 用户id(帐号)，企业内必须唯一。长度为1~64个字符（包括汉字和英文字母）
     * @param string $name 用户名称。长度为0~64个字符（包括汉字和英文字母，可为空）
     * @param int $gender 性别，整型。0表示男性，1表示女性
     * @param string $mobile 手机号码。企业内必须唯一
     * @param string $phone 电话号码
     * @param string $email 邮箱。长度为0~64个字符
     * @param array $dept 所属部门列表,不超过20个
     */
    public function update(int|string $userId, string $name, int $gender = 0, string $mobile = '', string $phone = '', string $email = '', array $dept = []): bool
    {
        $parameters = [
            'userId' => (string) $userId,
            'name' => $name,
            'gender' => $gender,
            'mobile' => $mobile,
            'phone' => $phone,
            'email' => $email,
            'dept' => $dept,
        ];

        $this->httpPostJson('/cgi/user/update', $parameters)->throw();

        return true;
    }

    /**
     * 更新职位信息.
     *
     * @param int|string $userId 用户id(帐号)，企业内必须唯一。长度为1~64个字符（包括汉字和英文字母）
     * @param int $deptId 部门Id。用户必须在该部门内
     * @param string $position 职务
     * @param int $weight 职务权重。用户拥有多个职务时，权重值越大的职务排序越靠前
     * @param int $sortId 用户在部门中的排序，值越大排序越靠前
     */
    public function updatePosition(int|string $userId, int $deptId, string $position = '', int $weight = 0, int $sortId = 0): bool
    {
        $parameters = [
            'userId' => (string) $userId,
            'deptId' => $deptId,
            'position' => $position,
            'weight' => $weight,
            'sortId' => $sortId,
        ];

        $this->httpPostJson('/cgi/user/positionupdate', $parameters)->throw();

        return true;
    }

    /**
     * 删除用户.
     * @param array|int $userId
     */
    public function delete(int|string $userId): bool
    {
        if (is_array($userId)) {
            $this->httpPostJson('/cgi/user/batchdelete', [
                'delList' => array_map(fn ($uid) => (string) $uid, $userId),
            ])->throw();

            return true;
        }

        $this->httpGet('/cgi/user/delete', ['userId' => $userId])->throw();

        return true;
    }

    /**
     * 用户详情.
     */
    public function get(int|string $userId): array
    {
        return $this->httpGet('/cgi/user/get', ['userId' => $userId])->throw()->json();
    }

    /**
     * 设置认证信息.
     *
     * @param int $authType 认证方式：0本地认证，2第三方认证
     * @param string $passwd 原始密码md5加密后转16进制的小写字符串
     */
    public function setAuth(int|string $userId, int $authType = 0, string $passwd = ''): bool
    {
        $parameters = [
            'userId' => (string) $userId,
            'authType' => $authType,
            'passwd' => md5($passwd),
        ];

        $this->httpPostJson('/cgi/user/setauth', $parameters)->throw();

        return true;
    }

    /**
     * 设置头像.
     */
    public function setAvatar(int|string $userId, string $file): bool
    {
        // 封装上传参数
        $parameters = [
            'userId' => (string) $userId,
        ];

        // 开始上传
        $this->httpUpload('/cgi/avatar/set', $file, $parameters)->throw();

        return true;
    }

    /**
     * 获取头像（头像二进制数据）.
     */
    public function getAvatar(int|string $userId, int $size = 0): string
    {
        return $this->httpGet('/cgi/avatar/get', ['userId' => $userId, 'size' => $size])->throw(true)->body(true);
    }

    /**
     * 单点登录.
     */
    public function identify(string $token): array
    {
        return $this->httpGet('/cgi/identify', ['token' => $token])->throw()->json('userInfo', []);
    }
}
