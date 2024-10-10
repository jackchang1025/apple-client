# Apple Client

## 项目介绍

Apple Client 是一个集成了 [Saloon HTTP](https://docs.saloon.dev/) 客户端库,用于模拟 Apple 浏览器客户端与各种服务进行交互。它提供了一个简单而灵活的接口,使开发者能够轻松地集成 Apple
的身份验证、账户管理和其他相关功能到他们的应用程序中。

注意，在使用 Apple Client 库时之前可能需要 逆向 Apple 登录、找到加密账号和密码的算法

主要特性:

- Apple ID 身份验证
- 账户管理
- 电话号码验证
- 安全代码验证
- 灵活的配置管理
- 强大的错误处理

## 系统要求

- PHP 8.2 或更高版本
- Composer
- ext-simplexml
- ext-dom
- ext-libxml

## 安装

使用 Composer 安装 Apple Client:

```bash
composer require weijiajia/apple-client
```

## 使用示例

### 基本用法

1. 创建 AppleClient 实例:

```php

use Psr\Log\LoggerInterface;use Psr\SimpleCache\CacheInterface;use Weijiajia\AppleClient;use Weijiajia\AppleClientFactory;use Weijiajia\Cookies\Cookies;use Weijiajia\Store\CacheStore;


$config =  Config::fromArray([
    'apple_auth' => [
    'url' => 'https://your_apple_auth_url',
 ]);
 
//如果你需要持久化用户 cookie 信息
$cookie = new Cookies(cache: new CacheInterface());

//如果你需要持久化用户信息并管理多个账号
$cookie = new Cookies(cache: new CacheInterface(),'your_account_name');

//如果你需要记录日志
$logger = new LoggerInterface();

//如果你想设置同步并持久化 headers 信息
 $headerRepositories = new CacheStore(
            cache: new CacheInterface(),
            key: 'your_account_name',
            defaultData: [
            'scnt' => 'value',
            ]
        );

//创建 AppleClient 实例:
$client = new AppleClient(config: $config, headerRepositories: $headerRepositories,cookieJar: $cookie,logger: $logger);

//或者你可以使用 AppleFactory 创建实例:
$factory = new AppleClientFactory(cache: $cache, logger: $logger);
$client = $factory->create('your_client_id', [
    'apple_auth' => [
    'url' => 'https://your_apple_auth_url',
    ],
]);

//自定义配置
$client->withConfig([
'timeOutInterval' => 30,
]);

//使用代理:
$client->setProxy('http://proxy.example.com:8080');
```

2. 执行Apple ID身份验证:

```php

// login
$response = $client->authLogin('user@example.com', 'your_password');

//双重认证
$response = $client->verifySecurityCode('your_security_code');

// get token:
$response = $client->token();

```

## 注意事项
本项目仅用于学习和研究目的，请勿用于非法用途。 使用可能违反 apple 的使用条款，请谨慎使用。 确保你的网络环境能够正常访问 apple 网站。

## 许可证

本项目采用 MIT 许可证。详情请见 [LICENSE](LICENSE) 文件。

## 联系方式

如果你有任何问题或建议,请联系:
- shadowmatthew1025@gmail.com