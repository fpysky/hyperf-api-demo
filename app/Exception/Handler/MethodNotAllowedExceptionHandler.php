<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\MethodNotAllowedHttpException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 方法未找到异常处理器.
 * @package App\Exception\Handler
 */
class MethodNotAllowedExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof MethodNotAllowedHttpException) {
            $message = ErrorCode::getMessage(ErrorCode::METHOD_NOT_ALLOWED);
            $this->stopPropagation();
            return $response
                ->withBody($this->buildStandardOutput($message, ErrorCode::METHOD_NOT_ALLOWED))
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus($throwable->getStatusCode());
        }
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
