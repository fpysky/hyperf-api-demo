<?php

declare(strict_types=1);

namespace App\JsonRpc\TestService;

interface TestServiceInterface
{
    public function add(int $a, int $b): int;

    public function create(int $a): int;
}
