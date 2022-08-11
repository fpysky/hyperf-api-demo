<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\ErrorCode;
use App\Constants\StatusCode;
use Throwable;

/**
 * 未授权访问异常.
 * @package App\Exception
 */
class UnauthorizedException extends AbstractException
{
    protected int $statusCode = StatusCode::UNAUTHORIZED;

    public function __construct(string $message = '未授权的访问', $code = ErrorCode::UNAUTHORIZED, Throwable $previous = null)
    {
        parent::__construct($message, (int) $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
