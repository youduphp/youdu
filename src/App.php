<?php

declare(strict_types=1);
/**
 * This file is part of youdusdk/youdu-php.
 *
 * @link     https://github.com/youdusdk/youdu-php
 * @document https://github.com/youdusdk/youdu-php/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduSdk\Youdu;

use YouduSdk\Youdu\Exception\ErrorCode;
use YouduSdk\Youdu\Exception\Exception;
use YouduSdk\Youdu\Http\ClientInterface;
use YouduSdk\Youdu\Messages\App\MessageInterface;
use YouduSdk\Youdu\Messages\App\PopWindow;
use YouduSdk\Youdu\Messages\App\SysMsg;
use YouduSdk\Youdu\Messages\App\Text;

class App
{
    protected Dept $dept;

    protected Group $group;

    protected Media $media;

    protected Session $session;

    protected User $user;

    public function __construct(protected Config $config, protected ?ClientInterface $client = null)
    {
        if ($client && ! $config->getClient()) {
            $config->setClient($client);
        }

        $this->dept = new Dept($config);
        $this->group = new Group($config);
        $this->media = new Media($config);
        $this->session = new Session($config);
        $this->user = new User($config);
    }

    public function dept(): Dept
    {
        return $this->dept;
    }

    public function group(): Group
    {
        return $this->group;
    }

    public function media(): Media
    {
        return $this->media;
    }

    public function session(): Session
    {
        return $this->session;
    }

    public function user(): User
    {
        return $this->user;
    }

    /**
     * 发送应用消息.
     */
    public function send(MessageInterface $message): bool
    {
        $encrypted = $this->config->getPacker()->pack($message->toJson());
        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $encrypted,
        ];

        $url = $this->config->getUrlGenerator()->generate('/cgi/msg/send');
        $resp = $this->client->post($url, $parameters);

        if ($resp['httpCode'] != 200) {
            throw new Exception('http request code ' . $resp['httpCode'], ErrorCode::$IllegalHttpReq);
        }

        $body = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($body['errcode'] !== 0) {
            throw new Exception($body['errmsg'], $body['errcode']);
        }

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
            $items = new Messages\App\Items\SysMsg();
            $items->addText($message);
            $message = new SysMsg($items);
        }

        if (! $message instanceof SysMsg) {
            throw new Exception('$message must instanceof' . SysMsg::class);
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
            'msg_encrypt' => $this->config->getPacker()->pack(json_encode([
                'account' => $account,
                'tip' => $tip,
                'count' => $msgCount,
            ], JSON_THROW_ON_ERROR)),
        ];

        $resp = $this->client->post($this->config->getUrlGenerator()->generate('/cgi/set.ent.notice'), $parameters);

        if ($resp['httpCode'] != 200) {
            throw new Exception('http request code ' . $resp['httpCode'], ErrorCode::$IllegalHttpReq);
        }

        $body = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($body['errcode'] !== 0) {
            throw new Exception($body['errmsg'], $body['errcode']);
        }

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
            'msg_encrypt' => $this->config->getPacker()->pack($message->toJson()),
        ];

        $resp = $this->client->post($this->config->getUrlGenerator()->generate('/cgi/popwindow'), $parameters);

        if ($resp['httpCode'] != 200) {
            throw new Exception('http request code ' . $resp['httpCode'], ErrorCode::$IllegalHttpReq);
        }

        $body = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($body['errcode'] !== 0) {
            throw new Exception($body['errmsg'], $body['errcode']);
        }

        return true;
    }
}
