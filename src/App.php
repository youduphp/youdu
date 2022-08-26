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

use YouduSdk\Youdu\Crypt\Prpcrypt;
use YouduSdk\Youdu\Exceptions\AccessTokenDoesNotExistException;
use YouduSdk\Youdu\Exceptions\ErrorCode;
use YouduSdk\Youdu\Exceptions\Exception;
use YouduSdk\Youdu\Http\ClientInterface;
use YouduSdk\Youdu\Messages\App\AbstractMessage;
use YouduSdk\Youdu\Messages\App\PopWindow;
use YouduSdk\Youdu\Messages\App\SysMsg;
use YouduSdk\Youdu\Messages\App\Text;

class App
{
    protected Prpcrypt $crypter;

    protected Dept $dept;

    protected Group $group;

    protected User $user;

    protected Session $session;

    protected Media $media;

    public function __construct(protected Config $config, protected ClientInterface $client)
    {
        $this->crypter = new Prpcrypt($config->getAesKey());
        $this->dept = new Dept($this);
        $this->group = new Group($this);
        $this->session = new Session($this);
        $this->media = new Media($this);
        $this->user = new User($this);
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

    public function config(): Config
    {
        return $this->config;
    }

    public function client(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Get access token.
     */
    public function getAccessToken(): string
    {
        $encrypted = $this->config->encryptMsg((string) time());

        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $encrypted,
        ];

        $url = $this->buildUrl('/cgi/gettoken', false);
        $resp = $this->client->post($url, $parameters);
        $body = json_decode($resp['body'], true, 512, JSON_THROW_ON_ERROR);

        if ($body['errcode'] != 0) {
            throw new Exception($body['errmsg'], $body['errcode']);
        }

        $decrypted = $this->config->decryptMsg($body['encrypt']);
        $decoded = json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR);

        return $decoded['accessToken'];
    }

    /**
     * 组装 URL.
     */
    public function buildUrl(string $uri = '', bool $withAccessToken = true): string
    {
        $uri = '/' . ltrim($uri, '/');

        if ($withAccessToken) {
            $token = $this->getAccessToken();

            if (! $token) {
                throw new AccessTokenDoesNotExistException('AccessToken does not exist', 1);
            }

            $uri .= "?accessToken={$token}";
        }

        return $uri;
    }

    /**
     * 发送应用消息.
     */
    public function send(AbstractMessage $message): bool
    {
        $encrypted = $this->config->encryptMsg($message->toJson());
        $parameters = [
            'buin' => $this->config->getBuin(),
            'appId' => $this->config->getAppId(),
            'encrypt' => $encrypted,
        ];

        $url = $this->buildUrl('/cgi/msg/send');
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
    public function sendToUser(string $toUser = '', AbstractMessage|string $message = ''): bool
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
    public function sendToDept(string $toDept = '', AbstractMessage|string $message = ''): bool
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
            'msg_encrypt' => $this->config->encryptMsg(json_encode([
                'account' => $account,
                'tip' => $tip,
                'count' => $msgCount,
            ], JSON_THROW_ON_ERROR)),
        ];

        $resp = $this->client->post($this->buildUrl('/cgi/set.ent.notice'), $parameters);

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
            'msg_encrypt' => $this->config->encryptMsg($message->toJson()),
        ];

        $resp = $this->client->post($this->buildUrl('/cgi/popwindow'), $parameters);

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
