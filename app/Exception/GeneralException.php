<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\ErrorCode;
use App\Constants\StatusCode;
use Throwable;

/**
 * 通用异常.
 * @package App\Exception
 */
class GeneralException extends AbstractException
{
    protected int $statusCode = StatusCode::SERVER_ERROR;

    public function __construct(
        $code,
        string $message = null,
        int $statusCode = StatusCode::SERVER_ERROR,
        Throwable $previous = null
    ) {
        if (is_null($message)) {
            $message = ErrorCode::getMessage($code);
            if (empty($message)) {
                $message = ErrorCode::getMessage(ErrorCode::SERVER_ERROR);
            }
        }
        $this->statusCode = $statusCode;
        parent::__construct($message, (int) $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
