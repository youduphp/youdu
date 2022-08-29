# youduphp/youdu

[![Latest Test](https://github.com/youduphp/youdu/workflows/tests/badge.svg)](https://github.com/youduphp/youdu/actions)
[![Latest Stable Version](https://poser.pugx.org/youduphp/youdu/v/stable.svg)](https://packagist.org/packages/youduphp/youdu)
[![Latest Unstable Version](https://poser.pugx.org/youduphp/youdu/v/unstable.svg)](https://packagist.org/packages/youduphp/youdu)
[![Total Downloads](https://img.shields.io/packagist/dt/youduphp/youdu)](https://packagist.org/packages/youduphp/youdu)
[![License](https://img.shields.io/packagist/l/youduphp/youdu)](https://github.com/friendsofhyperf/youdu)

The php sdk of youdu.

## Installation

```shell
composer require youduphp/youdu
```

## Usage

```php
use YouduPhp\Youdu\Application;
use YouduPhp\Youdu\Config;
use YouduPhp\Youdu\Kernel\Message\App\Text;

$config = new Config([
    'api' => 'http://10.0.0.188:7080',
    'buin' => 56565656,
    'app_id' => 'yd06AB76EC519B4130A802224B4C60F689',
    'aes_key' => 'A0aWSqDL5SV4fafQl3OavoVPUn6sx7xNnD+1hOoTeWk=',
]);

$app = new Application($config);
$msg = (new Text('hello world'))->toUser(10001);

$app->message()->send($msg);
```
