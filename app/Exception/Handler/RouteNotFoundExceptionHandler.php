<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Constants\StatusCode;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\NotFoundHttpException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 路由未找到异常处理器.
 * @package App\Exception\Handler
 */
class RouteNotFoundExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof NotFoundHttpException) {
            $this->stopPropagation();
            $message = ErrorCode::getMessage(ErrorCode::ROUTE_NOT_FOUND);
            return $response
                ->withBody($this->buildStandardOutput($message, ErrorCode::ROUTE_NOT_FOUND))
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(StatusCode::NOT_FOUND);
        }
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
