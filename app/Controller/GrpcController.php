<?php

declare(strict_types=1);

namespace App\Controller;

use App\GrpcClient\HiClient;
use Grpc\HiReply;
use Grpc\HiUser;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ServiceGovernanceNacos\NacosDriver;

class GrpcController
{
    /** @Inject */
    protected NacosDriver $nacosDriver;

    public function hello(): array
    {
        $client = new HiClient();

        $request = new HiUser();
        $request->setName('hyperf');
        $request->setSex(1);

        /** @var HiReply $reply */
        [$reply, $status] = $client->sayHello($request);

        $message = $reply->getMessage();
        $user = $reply->getUser();

        return [
            'message' => $message,
            'user' => $user,
            'status' => $status,
        ];
    }

    public function getUri(): string
    {
        $nodes = $this->nacosDriver->getNodes(env('NACOS_URI'), 'grpc.hi', [
            'protocol' => 'grpc',
        ]);
        if (empty($nodes)) {
            throw new \Exception('节点未找到');
        }
        $node = $nodes[0];
        return "{$node['host']}:{$node['port']}";
    }
}
