<?php

declare(strict_types=1);

namespace App\GrpcClient;

use Hyperf\Di\Annotation\Inject;
use Hyperf\ServiceGovernanceNacos\NacosDriver;

class BaseClient extends \Hyperf\GrpcClient\BaseClient
{
    protected string $serviceName;

    /** @Inject */
    protected NacosDriver $nacosDriver;

    public function __construct()
    {
        $uri = $this->getUri();
        parent::__construct($uri, ['credentials' => null]);
    }

    public function getUri(): string
    {
        $nodes = $this->nacosDriver->getNodes(env('NACOS_URI'), $this->serviceName, [
            'protocol' => 'grpc',
        ]);
        if (empty($nodes)) {
            throw new \RuntimeException('节点未找到');
        }
        $node = $nodes[0];
        return "{$node['host']}:{$node['port']}";
    }
}
