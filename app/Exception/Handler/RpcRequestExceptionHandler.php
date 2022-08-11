<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Constants\StatusCode;
use App\Extend\Log\Log;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\JsonRpc\ResponseBuilder;
use Hyperf\RpcClient\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RpcRequestExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof RequestException) {
            $this->stopPropagation();
            $code = $this->convertRequestCode((int) $throwable->getCode());
            $recordLog = false;
            if ($this->isRequestServerError($throwable) && isset($throwable->getThrowable()['class'])) {
                $isBusinessException = $this->isBusinessException($throwable->getThrowableClassName());

                if (! $isBusinessException) {
                    $recordLog = true;
                }

                // 远程服务内部错误，进行有条件错误输出
                if (isProd() && ! $isBusinessException) {
                    // 生产环境下 且 非业务逻辑异常，屏蔽错误输出
                    $message = ErrorCode::getMessage(ErrorCode::JSON_RPC_SERVER_ERROR);
                } else {
                    // 非生产环境下输出所有远程服务错误
                    $message = $throwable->getMessage();
                }
            } else {
                $recordLog = true;
                // 非服务内部错误，进行消息翻译
                $message = ErrorCode::getMessage($code);
            }

            if ($recordLog) {
                Log::get()->error('远程服务调用异常', $throwable->getThrowable());
            }

            return $response
                ->withBody($this->buildStandardOutput($message, $code))
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(StatusCode::SERVER_ERROR);
        }
        return $response;
    }

    /**
     * 确定当前异常处理程序是否应该处理异常.
     * @param Throwable $throwable
     * @return bool
     * @author fengpengyuan 2022/8/11
     * @modifier fengpengyuan 2022/8/11
     */
    public function isValid(Throwable $throwable): bool
    {
        return true;
    }

    /**
     * 异常是否是服务内部错误.
     * @param RequestException $throwable
     * @return bool
     * @author fengpengyuan 2022/8/11
     * @modifier fengpengyuan 2022/8/11
     */
    private function isRequestServerError(RequestException $throwable): bool
    {
        return $throwable->getCode() == ResponseBuilder::SERVER_ERROR;
    }

    /**
     * 是否是业务逻辑异常.
     * @param string $throwClass
     * @return bool
     * @author fengpengyuan 2022/8/11
     * @modifier fengpengyuan 2022/8/11
     */
    private function isBusinessException(string $throwClass): bool
    {
        switch ($throwClass) {
            case \Exception::class:
            case \RuntimeException::class:
            case 'app\Exception\BusinessException':
                return true;
            default:
                return false;
        }
    }

    /**
     * 转换rpc请求状态码
     * @param int $requestCode
     * @return int
     * @author fengpengyuan 2022/8/11
     * @modifier fengpengyuan 2022/8/11
     */
    private function convertRequestCode(int $requestCode): int
    {
        switch ($requestCode) {
            default:
            case ResponseBuilder::SERVER_ERROR:
                $code = ErrorCode::JSON_RPC_SERVER_ERROR;
                break;
            case ResponseBuilder::METHOD_NOT_FOUND:
                $code = ErrorCode::JSON_RPC_METHOD_NOT_FOUND;
                break;
            case ResponseBuilder::INTERNAL_ERROR:
                $code = ErrorCode::JSON_RPC_INTERNAL_ERROR;
                break;
            case ResponseBuilder::INVALID_PARAMS:
                $code = ErrorCode::JSON_RPC_INVALID_PARAMS;
                break;
            case ResponseBuilder::INVALID_REQUEST:
                $code = ErrorCode::JSON_RPC_INVALID_REQUEST;
                break;
            case ResponseBuilder::PARSE_ERROR:
                $code = ErrorCode::JSON_RPC_PARSE_ERROR;
                break;
        }
        return $code;
    }
}
