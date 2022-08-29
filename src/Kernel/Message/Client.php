<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Kernel\Message;

use YouduPhp\Youdu\Kernel\Exception\LogicException;
use YouduPhp\Youdu\Kernel\HttpClient\BaseClient;
use YouduPhp\Youdu\Kernel\Message\App\MessageInterface;
use YouduPhp\Youdu\Kernel\Message\App\PopWindow;
use YouduPhp\Youdu\Kernel\Message\App\SysMsg;
use YouduPhp\Youdu\Kernel\Message\App\Text;

class Client extends BaseClient
{
    /**
     * 发送应用消息.
     */
    public function send(MessageInterface $message): bool
    {
        $this->httpPost('/cgi/msg/send', $message->toArray())->throw();

        return true;
    }

    /**
     * 发送消息给用户.
     *
     * @param string $toUser 接收成员的帐号列表。多个接收者用竖线分隔，最多支持1000个
     */
    public function sendToUser(string $toUser = '', MessageInterface|string $message = ''): bool
    {
        if (is_string($message)) {
            $message = new Text($message);
        }

        $message->toUser($toUser);

        return $this->send($message);
    }

    /**
     * 发送消息至部门.
     *
     * @param string $toDept $toDept 接收部门id列表。多个接收者用竖线分隔，最多支持100个
     */
    public function sendToDept(string $toDept = '', MessageInterface|string $message = ''): bool
    {
        if (is_string($message)) {
            $message = new Text($message);
        }

        $message->toDept($toDept);

        return $this->send($message);
    }

    /**
     * 发送系统消息.
     */
    public function sendToAll(SysMsg|string $message, bool $onlineOnly = false): bool
    {
        if (is_string($message)) {
            $items = new App\Items\SysMsg();
            $items->addText($message);
            $message = new SysMsg($items);
        }

        if (! $message instanceof SysMsg) {
            throw new LogicException('$message must instanceof' . SysMsg::class);
        }

        $message->toAll($onlineOnly);

        return $this->send($message);
    }

    /**
     * 设置通知数.
     */
    public function setNoticeCount(string $account = '', string $tip = '', int $msgCount = 0): bool
    {
        $parameters = [
            'app_id' => $this->config->getAppId(),
            'msg_encrypt' => $this->packer->pack(json_encode([
                'account' => $account,
                'tip' => $tip,
                'count' => $msgCount,
            ], JSON_THROW_ON_ERROR)),
        ];

        $this->httpPost('/cgi/set.ent.notice', $parameters)->throw();

        return true;
    }

    /**
     * 应用弹窗.
     */
    public function popWindow(string $toUser = '', string $toDept = '', PopWindow $message = null): bool
    {
        if ($toUser) {
            $message->toUser($toUser);
        }

        if ($toDept) {
            $message->toDept($toDept);
        }

        $parameters = [
            'app_id' => $this->config->getAppId(),
            'msg_encrypt' => $this->packer->pack($message->toJson()),
        ];

        $this->httpPost('/cgi/popwindow', $parameters)->throw();

        return true;
    }
}
