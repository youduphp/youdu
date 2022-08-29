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

use ArrayAccess;
use Psr\Http\Message\ResponseInterface;
use YouduPhp\Youdu\Kernel\Exception\ErrorCode;
use YouduPhp\Youdu\Kernel\Exception\Exception;
use YouduPhp\Youdu\Kernel\Exception\RequestException;
use YouduPhp\Youdu\Kernel\Util\Packer\PackerInterface;

/**
 * @mixin \GuzzleHttp\Psr7\Response
 */
class Response implements ArrayAccess
{
    protected int $errCode = 0;

    protected string $errMsg = '';

    private string $body = '';

    private array $json = [];

    public function __construct(protected ResponseInterface $response, protected PackerInterface $packer)
    {
        $data = json_decode((string) $response->getBody(), true);

        $this->errCode = (int) ($data['errcode'] ?? -1);
        $this->errMsg = (string) ($data['errmsg'] ?? '');

        if ($encrypt = $data['encrypt'] ?? '') {
            $this->body = $this->packer->unpack($encrypt);
            $this->json = (array) json_decode($this->body, true);
        }
    }

    public function __call($name, $arguments)
    {
        return $this->response->{$name}(...$arguments);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->json[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->json[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }

    public function getPsrResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getErrCode(): int
    {
        return $this->errCode;
    }

    public function getErrMsg(): string
    {
        return $this->errMsg;
    }

    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    public function body(): string
    {
        return $this->body;
    }

    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    public function json(string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->json;
        }

        $result = $this->json;

        foreach (explode('.', $key) as $k) {
            $result = $result[$k] ?? $default;
        }

        return $result;
    }

    public function toArray(): array
    {
        return $this->json;
    }

    public function throw(): self
    {
        if ($this->status() != 200) {
            throw new RequestException('HTTP status code ' . $this->status(), ErrorCode::$IllegalHttpReq);
        }

        if ($this->getErrCode() !== ErrorCode::$OK) {
            throw new Exception($this->getErrMsg(), $this->getErrCode());
        }

        return $this;
    }
}
