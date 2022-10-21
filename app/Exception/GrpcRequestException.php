<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\ErrorCode;
use Throwable;

class GrpcRequestException extends AbstractException
{
    public function __construct($message = '', $code = ErrorCode::GRPC_RPC_UNKNOWN_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, (int) $code, $previous);
    }

    public function getStatusCode(): int
    {
        return 500;
    }
}
