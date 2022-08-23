<?php

declare(strict_types=1);

namespace App\GrpcClient;

use Grpc\Req;
use Grpc\TokenTestResult;

class TestClient extends BaseClient
{
    public function __construct()
    {
        $this->serviceName = 'grpc.test';
        parent::__construct();
    }

    public function tokenTest(Req $req): array
    {
        return $this->_simpleRequest(
            '/' . $this->serviceName . '/tokenTest',
            $req,
            [TokenTestResult::class, 'decode']
        );
    }
}
