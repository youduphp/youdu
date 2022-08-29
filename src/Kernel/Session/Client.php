<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Kernel\Session;

use YouduPhp\Youdu\Kernel\Exception\InvalidArgumentException;
use YouduPhp\Youdu\Kernel\Exception\LogicException;
use YouduPhp\Youdu\Kernel\HttpClient\BaseClient;
use YouduPhp\Youdu\Kernel\Message\Session\MessageInterface;
use YouduPhp\Youdu\Kernel\Message\Session\Text;

class Client extends BaseClient
{
    /**
     * 创建会话.
     *
     * @param string $title 会话标题。最多允许64个字符
     * @param string $creator 会话创建者账号。最多允许64个字符
     * @param array $member 会话成员账号列表。包括创建者，多人会话的成员数必须在3人及以上
     * @param string $type 会话类型。仅支持多人会话(multi)
     */
    public function create(string $title, string $creator = '', array $member = [], string $type = 'multi'): array
    {
        if (count($member) < 3) {
            throw new InvalidArgumentException('Members too less', 1);
        }

        $member = array_map(fn ($item) => (string) $item, $member);

        $parameters = [
            'title' => $title,
            'creator' => $creator,
            'type' => $type,
            'member' => $member,
        ];

        return $this->httpPost('/cgi/session/create', $parameters)->throw()->json();
    }

    /**
     * 修改会话.
     *
     * @param string $sessionId 会话id
     * @param string $opUser 操作者账号
     * @param string $title 会话标题
     * @param array $addMember 新增会话成员账号列表
     * @param array $delMember 删除会话成员账号列表
     */
    public function update(string $sessionId, string $opUser = '', string $title = '', array $addMember = [], array $delMember = []): array
    {
        $addMember = array_map(fn ($item) => (string) $item, $addMember);
        $delMember = array_map(fn ($item) => (string) $item, $delMember);

        $parameters = [
            'sessionId' => $sessionId,
            'title' => $title,
            'opUser' => $opUser,
            'addMember' => $addMember,
            'delMember' => $delMember,
        ];

        return $this->httpPost('/cgi/session/update', $parameters)->throw()->json();
    }

    /**
     * 获取会话.
     */
    public function info(string $sessionId): array
    {
        return $this->httpGet('/cgi/session/get', ['sessionId' => $sessionId])->throw()->json();
    }

    /**
     * 发送会话消息.
     *
     * @throws LogicException
     */
    public function send(MessageInterface $message): bool
    {
        $this->httpPost('/cgi/session/send', $message->toArray())->throw();

        return true;
    }

    /**
     * 发送个人会话消息.
     */
    public function sendToUser(string $sender, string $receiver, MessageInterface|string $message = ''): bool
    {
        if (is_string($message)) {
            $message = new Text($message);
        }

        $message->sender($sender);
        $message->receiver($receiver);

        return $this->send($message);
    }

    /**
     * 发送多人会话消息.
     */
    public function sendToSession(string $sender, string $sessionId, MessageInterface|string $message = ''): bool
    {
        if (is_string($message)) {
            $message = new Text($message);
        }

        $message->sender($sender);
        $message->session($sessionId);

        return $this->send($message);
    }
}
