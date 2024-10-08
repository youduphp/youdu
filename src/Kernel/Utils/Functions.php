<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */

namespace YouduPhp\Youdu\Kernel\Utils;

use Closure;

/**
 * Call the given Closure with the given value then return the value.
 *
 * @param mixed $value
 */
function tap($value, ?callable $callback = null)
{
    if (is_null($callback)) {
        return new HigherOrderTapProxy($value);
    }

    $callback($value);

    return $value;
}

/**
 * Return the given value, optionally passed through the given callback.
 *
 * @template TValue
 *
 * @param TValue $value
 * @param (callable(TValue): TValue)|null $callback
 * @return TValue
 */
function with($value, ?callable $callback = null)
{
    return is_null($callback) ? $value : $callback($value);
}

/**
 * Return the default value of the given value.
 *
 * @param mixed $value
 */
function value($value, ...$args)
{
    return $value instanceof Closure ? $value(...$args) : $value;
}

function array_get(array $array, ?string $key = null, $default = null)
{
    if (is_null($key)) {
        return $array;
    }

    if (array_key_exists($key, $array)) {
        return $array[$key];
    }

    foreach (explode('.', $key) as $segment) {
        if (! is_array($array) || ! array_key_exists($segment, $array)) {
            return $default;
        }

        $array = $array[$segment];
    }
    return $array;
}
