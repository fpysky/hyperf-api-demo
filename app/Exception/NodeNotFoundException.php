<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\ErrorCode;
use Throwable;

class NodeNotFoundException extends GrpcRequestException
{
    public function __construct($message = '服务节点未找到', $code = ErrorCode::GRPC_RPC_NODE_NOT_FOUND, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
