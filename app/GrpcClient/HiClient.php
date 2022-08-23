<?php

namespace App\GrpcClient;

use Grpc\HiReply;
use Grpc\HiUser;

class HiClient extends BaseClient
{
    public function __construct()
    {
        $this->serviceName = 'grpc.hi';
        parent::__construct();
    }

    public function sayHello(HiUser $argument)
    {
        return $this->_simpleRequest(
            '/' . $this->serviceName . '/sayHello',
            $argument,
            [HiReply::class, 'decode']
        );
    }
}