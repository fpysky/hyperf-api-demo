<?php

declare(strict_types=1);

namespace App\Extend\Nacos;

use App\Exception\NodeNotFoundException;
use App\Extend\Log\Log;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ServiceGovernanceNacos\NacosDriver;

trait Node
{
    /** @Inject */
    protected NacosDriver $nacosDriver;

    /**
     * 获取节点主机.
     * @param string $serviceName
     * @param string $protocol
     * @param string $balance
     * @return string
     * @author fengpengyuan 2022/8/30
     * @modifier fengpengyuan 2022/8/30
     */
    public function getNodeUri(string $serviceName, string $protocol, string $balance = 'random'): string
    {
        $nodes = $this->nacosDriver
            ->getNodes(env('NACOS_URI'), $serviceName, ['protocol' => $protocol]);

        if (empty($nodes)) {
            Log::get()->error('服务节点未找到', [
                'nacosUri' => env('NACOS_URI'),
                'serviceName' => $serviceName,
                'protocol' => $protocol,
                'balance' => $balance,
            ]);
            throw new NodeNotFoundException('服务节点未找到');
        }

        // todo: 随便写一个随机数负载均衡 后面再优化
        $nodeNum = mt_rand(0, count($nodes) - 1);

        $node = $nodes[$nodeNum];

        return "{$node['host']}:{$node['port']}";
    }

    /**
     * 查询节点是否存在
     * @param string $serviceName
     * @param string $protocol
     * @return bool
     * @author fengpengyuan 2022/10/10
     * @modifier fengpengyuan 2022/10/10
     */
    public function nodeExist(string $serviceName, string $protocol): bool
    {
        $nodes = $this->nacosDriver
            ->getNodes(env('NACOS_URI'), $serviceName, ['protocol' => $protocol]);

        return empty($nodes) ? false : true;
    }
}
