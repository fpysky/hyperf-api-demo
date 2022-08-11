<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Constants\StatusCode;
use App\Extend\Log\Log;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 默认的异常处理器.
 * @package App\Exception\Handler
 */
class AppExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    /** @Inject */
    protected StdoutLoggerInterface $stdoutLogger;

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->stdoutLogger->error(sprintf(
            '%s[%s] in %s',
            $throwable->getMessage(),
            $throwable->getLine(),
            $throwable->getFile()
        ));
        $this->stdoutLogger->error($throwable->getTraceAsString());

        Log::get()->error($throwable->getMessage(), [
            'line' => $throwable->getLine(),
            'file' => $throwable->getFile(),
        ]);

        if (isProd()) {
            $message = ErrorCode::getMessage(ErrorCode::SERVER_ERROR);
        } else {
            $message = $throwable->getMessage();
        }

        return $response
            ->withBody($this->buildStandardOutput($message, ErrorCode::SERVER_ERROR))
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(StatusCode::SERVER_ERROR);
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
