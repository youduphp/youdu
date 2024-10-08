<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
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
use YouduPhp\Youdu\Kernel\Utils\Packer\PackerInterface;
use YouduPhp\Youdu\Kernel\Utils\Traits\Conditionable;
use YouduPhp\Youdu\Kernel\Utils\Traits\Tappable;

use function YouduPhp\Youdu\Kernel\Utils\array_get;
use function YouduPhp\Youdu\Kernel\Utils\tap;

/**
 * @mixin \GuzzleHttp\Psr7\Response
 */
class Response implements ArrayAccess
{
    use Tappable;
    use Conditionable;

    protected int $errCode = 0;

    protected string $errMsg = '';

    private string $body = '';

    private array $json = [];

    private int $statusCode = 200;

    public function __construct(protected ResponseInterface $response, protected PackerInterface $packer)
    {
        $this->body = (string) $response->getBody();
        $this->statusCode = $response->getStatusCode();

        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (! empty($data)) {
            $this->errCode = (int) ($data['errcode'] ?? -1);
            $this->errMsg = (string) ($data['errmsg'] ?? '');

            if ($encrypted = $data['encrypt'] ?? '') {
                $decrypted = $this->packer->unpack($encrypted);
                $this->json = (array) json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR);
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

    public function body($unpack = false): string
    {
        if ($unpack) {
            return $this->packer->unpack($this->body);
        }

        return $this->body;
    }

    public function header($header): string
    {
        return $this->response->getHeaderLine($header);
    }

    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    public function status(): int
    {
        return $this->statusCode;
    }

    public function reason(): string
    {
        return $this->response->getReasonPhrase();
    }

    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function ok(): bool
    {
        return $this->status() === 200;
    }

    public function failed(): bool
    {
        return $this->serverError() || $this->clientError();
    }

    public function clientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    public function serverError(): bool
    {
        return $this->status() >= 500;
    }

    public function json(?string $key = null, $default = null)
    {
        return array_get($this->json, $key, $default);
    }

    public function toArray(): array
    {
        return $this->json;
    }

    public function throw(bool $onlyCheckHttpStatusCode = false): self
    {
        return tap($this, function () use ($onlyCheckHttpStatusCode) {
            if (! $this->ok()) {
                throw new RequestException('HTTP status code ' . $this->status(), ErrorCode::$IllegalHttpReq);
            }

            if (! $onlyCheckHttpStatusCode && $this->getErrCode() !== ErrorCode::$OK) {
                throw new LogicException($this->getErrMsg(), $this->getErrCode());
            }
        });
    }
}
