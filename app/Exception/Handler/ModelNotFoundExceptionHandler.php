<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Constants\StatusCode;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 记录未找到异常处理器.
 * @package App\Exception\Handler
 */
class ModelNotFoundExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof ModelNotFoundException) {
            $message = ErrorCode::getMessage(ErrorCode::MODEL_NOT_FOUND);
            $this->stopPropagation();
            return $response
                ->withBody($this->buildStandardOutput($message, ErrorCode::MODEL_NOT_FOUND))
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
