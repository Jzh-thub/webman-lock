<?php

use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\Store\RedisStore;

return [
    'enable' => true,
    'storage' => [
        'default' => 'redis',// file/redis 建议使用redis,file 不支持 ttl
        'file' => [
            'class' => FlockStore::class,
            'construct' => [
                'lockPath' => runtime_path() . DIRECTORY_SEPARATOR . 'lock'
            ],
        ],
        'redis' => [
            'class' => RedisStore::class,
            'construct' => function () {
                return [
                    'redis' => \support\Redis::connection('default')->client(),
                ];
            }
        ],
        'default_config' => [
            'ttl' => 300,//默认锁超时时间
            'auto_release' => true,//是否自动释放,建议设置为true
            'prefix' => 'lock_',// 锁前缀
        ]
    ]

];