<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\ErrorCode;
use App\Constants\StatusCode;
use Throwable;

/**
 * 参数错误异常.
 * @package App\Exception
 */
class UnprocessableEntityException extends AbstractException
{
    protected int $statusCode = StatusCode::UNPROCESSABLE_ENTITY;

    public function __construct(string $message = '参数错误', $code = ErrorCode::UNPROCESSABLE_ENTITY, Throwable $previous = null)
    {
        parent::__construct($message, (int) $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
