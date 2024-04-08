<?php

namespace Jzh\Lock;

use support\Container;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

class Locker
{
    public static function __callStatic($name, $arguments)
    {
        $key = $arguments[0] ?? '';
        unset($arguments[0]);
        return static::createLock($name . $key, ...$arguments);
    }

    /**
     * 默认配置
     * @var null
     */
    protected static $defaultConfig = null;

    /**
     * 创建锁
     * @param string $key
     * @param float|null $ttl 锁超时时间
     * @param bool|null $autoRelease 是否自动释放锁
     * @param string|null $prefix 锁前缀
     * @return LockInterface
     */
    protected static function createLock(string $key, ?float $ttl = null, ?bool $autoRelease = null, ?string $prefix = null): LockInterface
    {
        if (static::$defaultConfig === null) {
            static::$defaultConfig = config('plugin.jzh.lock.app.storage.default_config', []);
        }
        $config = static::$defaultConfig;
        $ttl = $ttl !== null ? $ttl : ($config['ttl'] ?? 300);
        $autoRelease = $autoRelease !== null ? $autoRelease : ($config['auto_release'] ?? true);
        $prefix = $prefix !== null ? $prefix : ($config['prefix'] ?? 'lock_');
        return static::getLockFactory()->createLock($prefix . $key, $ttl, $autoRelease);

    }

    /**
     * @var null|LockFactory
     */
    protected static ?LockFactory $factory = null;

    /**
     * @return LockFactory
     */
    protected static function getLockFactory(): LockFactory
    {
        if (static::$factory === null) {
            $storage = config('plugin.jzh.lock.app.storage');
            $storageConfig = $storage[$storage['default']];
            if (is_callable($storageConfig['construct'])) {
                $storageConfig['construct'] = call_user_func($storageConfig['construct']);
            }
            $storageInstance = Container::make($storageConfig['class'], $storageConfig['construct']);
            static::$factory = new LockFactory($storageInstance);
        }
        return static::$factory;
    }
}