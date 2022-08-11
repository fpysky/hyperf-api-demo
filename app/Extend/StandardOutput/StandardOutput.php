<?php

declare(strict_types=1);

namespace App\Extend\StandardOutput;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Throwable;

/**
 * 标准输出.
 * @package App\Extend\StandardOutput
 */
trait StandardOutput
{
    /**
     * 构建标准输出并返回.
     * @param string $message
     * @param $code
     * @param null $data
     * @return SwooleStream
     * @author fengpengyuan 2022/8/11
     * @modifier fengpengyuan 2022/8/11
     */
    public function buildStandardOutput(string $message, $code, $data = null): SwooleStream
    {
        return new SwooleStream(json_encode($this->buildStruct($message, $code, $data)));
    }

    /**
     * 构建标准输出并返回.
     * @param Throwable $throwable
     * @return SwooleStream
     * @author fengpengyuan 2022/8/11
     * @modifier fengpengyuan 2022/8/11
     */
    public function buildStandardOutputByThrowable(Throwable $throwable): SwooleStream
    {
        return new SwooleStream(json_encode($this->buildStructByThrowable($throwable)));
    }

    /**
     * 构建标准输出.
     * @param string $message
     * @param $code
     * @param null $data
     * @return array
     * @author fengpengyuan 2022/8/11
     * @modifier fengpengyuan 2022/8/11
     */
    public function buildStruct(string $message, $code, $data = null): array
    {
        if (is_null($data)) {
            $data = new \stdClass();
        }
        return [
            'data' => $data,
            'message' => (string) $message,
            'code' => $code,
        ];
    }

    /**
     * 构建标准输出.
     * @param Throwable $throwable
     * @return array
     * @author fengpengyuan 2022/8/11
     * @modifier fengpengyuan 2022/8/11
     */
    public function buildStructByThrowable(Throwable $throwable): array
    {
        return self::buildStruct((string) $throwable->getMessage(), (string) $throwable->getCode());
    }
}
