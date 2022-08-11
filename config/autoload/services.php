<?php

declare(strict_types=1);

use App\JsonRpc\TestService\TestServiceInterface;

return [
    'enable' => [
        // 开启服务发现
        'discovery' => true,
        // 开启服务注册
        'register' => false,
    ],
    // 服务消费者相关配置
    'consumers' => value(function () {
        $consumers = [];
        $services = [
            'TestService' => TestServiceInterface::class,
        ];
        foreach ($services as $name => $interface) {
            $consumers[] = [
                'name' => $name,
                'service' => $interface,
                'id' => $interface,
                'protocol' => 'jsonrpc-http',
                'load_balancer' => 'random',
                'registry' => [
                    'protocol' => 'consul',
                    'address' => env('CONSUL_URI', 'http://127.0.0.1:8500'),
                ],
                'options' => [
                    'connect_timeout' => 5.0,
                    'recv_timeout' => 5.0,
                    'settings' => [
                        'open_eof_split' => true,
                        'package_eof' => "\r\n",
                    ],
                    'retry_count' => 2,
                    'retry_interval' => 100, // 重试间隔（毫秒）
                    'pool' => [
                        'min_connections' => 1,
                        'max_connections' => 32,
                        'connect_timeout' => 10.0,
                        'wait_timeout' => 3.0,
                        'heartbeat' => -1,
                        'max_idle_time' => 60.0,
                    ],
                ],
            ];
        }
        return $consumers;
    }),
    // 服务提供者相关配置
    'providers' => [],
    // 服务驱动相关配置
    'drivers' => [
        'consul' => [
            'uri' => env('CONSUL_URI', 'http://127.0.0.1:8500'),
            'token' => '',
            'check' => [
                'deregister_critical_service_after' => '90m',
                'interval' => '1s',
            ],
        ],
    ],
];
