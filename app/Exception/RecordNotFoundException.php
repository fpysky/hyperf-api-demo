<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\StatusCode;
use Throwable;

/**
 * 记录未找到异常.
 * @package App\Exception
 */
class RecordNotFoundException extends AbstractException
{
    public int $statusCode = StatusCode::NOT_FOUND;

    public function __construct(string $message = '记录未找到', $code = StatusCode::NOT_FOUND, Throwable $previous = null)
    {
        parent::__construct($message, (int) $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
