# webman-lock/symfony-lock

[symfony/lock](https://packagist.org/packages/symfony/lock) for webman


## 介绍

在 webman 中简化使用业务锁功能

解决以下问题：

- 并发业务操作有时候需要锁来防止并发导致的数据插入或更新问题
- 单独使用 `symfony/lock` 时一般使用 `$factory->createLock('key')`，此时 key 是一个字符串，不利于后期维护或多处使用


## 安装
```bash
compoer require jzh/lock
```

## 使用

#### 方案1
定义一个自己的 Locker 类，比如：`mine\facade\Locker.php`，继承 `Jzh\Lock\Locker`

然后在类上方加入注释（用于代码提示），举例如下：

```php
<?php

namespace mine\facade;

use Symfony\Component\Lock\LockInterface;

/**
 * 业务锁
 * @package mine\facade
 * @method static LockInterface Order(string $key, float $ttl = null, bool $autoRelease = null, string $prefix = null) 创建订单锁
 * @method static LockInterface Payment(string $key, float $ttl = null, bool $autoRelease = null, string $prefix = null) 创建支付锁
 */
class Locker extends \Jzh\Lock\Locker
{

}
```
#### 方案2

直接使用Locker类，举例如下：
```php
<?php

namespace app\controller;

use Jzh\Lock\Locker;

class Cash {
    public function changeCash()
    {
        $lock = Locker::lock($key);
        if (!$lock->acquire()) {
            throw new \Exception('操作太频繁，请稍后再试');
        }
        try {
            // 业务逻辑
        } finally {
            $lock->release();
        }
        
        return 'ok';
    }
}
```
更多操作参考：[symfony/lock 文档](https://symfony.com/doc/current/components/lock.html)