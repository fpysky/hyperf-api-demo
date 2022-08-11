<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\AbstractException;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 通用异常处理器.
 * @package App\Exception\Handler
 */
class GeneralExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof AbstractException) {
            $this->stopPropagation();
            return $response
                ->withBody($this->buildStandardOutputByThrowable($throwable))
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
