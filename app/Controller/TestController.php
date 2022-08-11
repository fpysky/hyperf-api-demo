<?php

declare(strict_types=1);

namespace App\Controller;

use App\JsonRpc\TestService\TestServiceInterface;
use Hyperf\Di\Annotation\Inject;

class TestController extends AbstractController
{
    /** @Inject */
    protected TestServiceInterface $testService;

    public function test(): array
    {
        $result = $this->testService->add(1, 2);
        $aa = $this->testService->create(11);
        return [
            'data' => $result,
            'message' => 'ok!',
            'code' => 200000,
        ];
    }
}
