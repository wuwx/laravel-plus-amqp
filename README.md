# laravel-plus-amqp

Laravel AMQP 扩展，基于 PHP `ext-amqp` 提供连接、交换机、队列的简单管理与发布/消费封装。

## 特性

- 通过配置统一管理 AMQP 连接、交换机与队列
- 首次使用时自动声明 exchange/queue，并完成 bindings
- 支持 Facade 与容器单例两种使用方式

## 环境要求

- PHP 扩展：`ext-amqp`（`composer.json` 中为 suggest）
- Laravel 版本：`illuminate/support` ~5.1 或 ^6 ~ ^12

## 安装

```bash
composer require wuwx/laravel-plus-amqp
```

### 服务提供者

Laravel 5.5+ 会自动发现服务提供者：

- `Wuwx\LaravelPlusAmqp\LaravelPlusAmqpServiceProvider`

Laravel 5.4 及以下版本需手动注册：

```php
// config/app.php
'providers' => [
    Wuwx\LaravelPlusAmqp\LaravelPlusAmqpServiceProvider::class,
],

'aliases' => [
    'Amqp' => Wuwx\LaravelPlusAmqp\AmqpFacade::class,
],
```

### 发布配置

```bash
php artisan vendor:publish --tag=config
```

发布后配置文件位于：`config/amqp.php`。

## 快速上手

1. 安装扩展并确保 `ext-amqp` 可用
2. 发布配置并设置连接信息
3. 使用 Facade 发布/消费消息

### 示例环境变量

```dotenv
AMQP_HOST=127.0.0.1
AMQP_PORT=5672
AMQP_VHOST=/
AMQP_LOGIN=guest
AMQP_PASSWORD=guest
```

## 配置说明

默认配置来自 `config/amqp.php`：

```php
return [
    'defaults' => [
        'connection' => 'default',
        'exchange' => 'default',
        'queue' => 'default',
    ],
    'connections' => [
        'default' => [
            'host' => env('AMQP_HOST', 'localhost'),
            'port' => env('AMQP_PORT', 5672),
            'vhost' => env('AMQP_VHOST', '/'),
            'login' => env('AMQP_LOGIN', 'guest'),
            'password' => env('AMQP_PASSWORD', 'guest'),
        ],
    ],
    'exchanges' => [
        'default' => [
            'connection' => 'default',
            'name'       => 'exchange_name',
            'type'       => 'topic',
            'flags'      => AMQP_DURABLE,
        ],
    ],
    'queues' => [
        'default' => [
            'connection' => 'default',
            'bindings' => [
                ['exchange_name', 'routing_key'],
            ],
        ],
    ],
];
```

- `connections` 数组会作为 `AMQPConnection` 的参数传入。
- `exchanges` 首次调用时会自动声明（`declareExchange`）。
- `queues` 首次调用时会自动声明并执行 `bindings`（`bind`）。
- `bindings` 结构为 `[exchange, routing_key]`，等价于 `$queue->bind($exchange, $routingKey)`。

## 使用方式

包内通过容器单例 `amqp` 提供管理器，也提供 Facade：`Wuwx\LaravelPlusAmqp\AmqpFacade`。

### 发布消息

```php
use Wuwx\LaravelPlusAmqp\AmqpFacade as Amqp;

Amqp::publish(
    'payload',
    'routing.key',
    AMQP_NOPARAM,
    ['content_type' => 'text/plain']
);
```

### 消费消息

```php
use Wuwx\LaravelPlusAmqp\AmqpFacade as Amqp;

Amqp::consume(function ($message, $queue) {
    // 处理消息
    $queue->ack($message->getDeliveryTag());
});
```

> `publish` 与 `consume` 的参数会直接透传给 `AMQPExchange::publish` 与 `AMQPQueue::consume`，
> 具体参数请参考 `ext-amqp` 文档。

### 使用指定连接/交换机/队列

```php
$manager = app('amqp');

$connection = $manager->connection('default');
$exchange = $manager->exchange('default');
$queue = $manager->queue('default');

$exchange->publish('payload', 'routing.key');
```

## 项目结构

```
.
├── config
│   └── amqp.php              # 默认配置
├── src
│   ├── AmqpFacade.php         # Facade
│   ├── AmqpManager.php        # 管理器（connection/exchange/queue）
│   └── LaravelPlusAmqpServiceProvider.php
├── tests
│   └── TestCase.php
└── composer.json
```

## 开发与测试

```bash
vendor/bin/phpunit
```