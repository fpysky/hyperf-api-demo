<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 参数错误异常处理器.
 * @package App\Exception\Handler
 */
class ValidationExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof ValidationException) {
            $this->stopPropagation();
            $body = $this->buildStandardOutput(
                $throwable->validator->errors()->first(),
                ErrorCode::UNPROCESSABLE_ENTITY
            );
            return $response
                ->withBody($body)
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus($throwable->status);
        }
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
