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
use YouduPhp\Youdu\Kernel\Exception\LogicException;
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

    private int $statusCode = 200;

    public function __construct(protected ResponseInterface $response, protected PackerInterface $packer)
    {
        $this->body = (string) $response->getBody();
        $this->statusCode = $response->getStatusCode();

        $data = json_decode((string) $response->getBody(), true);

        if (! empty($data)) {
            $this->errCode = (int) ($data['errcode'] ?? -1);
            $this->errMsg = (string) ($data['errmsg'] ?? '');

            if ($encrypted = $data['encrypt'] ?? '') {
                $decrypted = $this->packer->unpack($encrypted);
                $this->json = (array) json_decode($decrypted, true);
            }
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
        return $this->statusCode;
    }

    public function body($decrypt = false): string
    {
        if ($decrypt) {
            return $this->packer->unpack($this->body);
        }

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

    public function throw(bool $onlyCheckHttpStatusCode = false): self
    {
        if ($this->status() != 200) {
            throw new RequestException('HTTP status code ' . $this->status(), ErrorCode::$IllegalHttpReq);
        }

        if (! $onlyCheckHttpStatusCode && $this->getErrCode() !== ErrorCode::$OK) {
            throw new LogicException($this->getErrMsg(), $this->getErrCode());
        }

        return $this;
    }
}
