<?php

declare(strict_types=1);

namespace App\GrpcClient;

use App\Constants\ErrorCode;
use App\Constants\StatusCode;
use App\Exception\GeneralException;
use App\Exception\NodeNotFoundException;
use App\Extend\Log\Log;
use App\Extend\Nacos\Node as NacosNode;
use Google\Protobuf\Internal\Message;
use Hyperf\GrpcClient\BaseClient as Base;
use InvalidArgumentException;

class BaseClient extends Base
{
    use NacosNode;

    /** 服务名称 */
    protected string $serviceName = '';

    /** 路由前缀 */
    protected string $serviceRoutePrefix = '';

    public function __construct()
    {
        try {
            $nodeUri = $this->getNodeUri($this->serviceName, 'grpc');
        } catch (NodeNotFoundException $exception) {
            $nodeUri = '';
        }
        parent::__construct($nodeUri, ['credentials' => null]);
    }

    /**
     * 请求远程方法 自动处理远程返回的code/msg/data.
     * @param string $method
     * @param Message $argument
     * @param array $deserialize
     * @param array $metadata
     * @param array $options
     * @return mixed
     * @author fengpengyuan 2022/8/29
     * @modifier fengpengyuan 2022/8/29
     */
    protected function request(
        string $method,
        Message $argument,
        array $deserialize,
        array $metadata = [],
        array $options = []
    ) {
        $response = $this->rowRequest($method, $argument, $deserialize, $metadata, $options);

        if (! method_exists($response, 'getCode')
            || ! method_exists($response, 'getMsg')
            || ! method_exists($response, 'getData')
        ) {
            Log::get()->error('远程服务方法返回结构异常', [
                'method' => $method,
                'argument' => $argument,
                'deserialize' => $deserialize,
                'metadata' => $metadata,
                'options' => $options,
            ]);
            throw new GeneralException(ErrorCode::GRPC_RPC_SERVER_ERROR, '远程服务方法返回结构异常');
        }

        if (! is_numeric($response->getCode())) {
            Log::get()->error('远程服务方法返回数据异常', [
                'method' => $method,
                'argument' => $argument,
                'deserialize' => $deserialize,
                'metadata' => $metadata,
                'options' => $options,
            ]);
            throw new GeneralException(ErrorCode::GRPC_RPC_SERVER_ERROR, '远程服务方法返回数据异常');
        }

        if ($response->getCode() != 0) {
            // 将服务返回的code前两位截取作为返回的http状态码
            $statusCode = (int) substr((string) $response->getCode(), 0, 3);
            // 保险起见，如果不是非标准的状态码，将设置成500
            if (! in_array($statusCode, StatusCode::STATUS_CODE_ARRAY)) {
                $statusCode = StatusCode::SERVER_ERROR;
            }

            throw new GeneralException($response->getCode(), (string) $response->getMsg(), $statusCode);
        }

        return $response->getData();
    }

    /**
     * 请求远程方法.
     * @param string $method
     * @param Message $argument
     * @param array $deserialize
     * @param array $metadata
     * @param array $options
     * @return mixed
     * @author fengpengyuan 2022/8/29
     * @modifier fengpengyuan 2022/8/29
     */
    protected function rowRequest(
        string $method,
        Message $argument,
        array $deserialize,
        array $metadata = [],
        array $options = []
    ) {
        try {
            [$response, $status] = $this->_simpleRequest($method, $argument, $deserialize, $metadata, $options);
        } catch (InvalidArgumentException $exception) {
            if (! $this->nodeExist($this->serviceName, 'grpc')) {
                throw new NodeNotFoundException('服务节点未找到');
            }

            throw new InvalidArgumentException($exception->getMessage());
        }

        if (is_null($deserialize[0]) && is_null($response)) {
            return null;
        }

        if (is_string($deserialize[0]) && is_a($response, $deserialize[0])) {
            return $response;
        }

        if (isProd()) {
            $message = ErrorCode::getMessage(ErrorCode::GRPC_RPC_UNKNOWN_ERROR);
        } else {
            if (is_string($response)) {
                $message = $response;
            } else {
                $message = ErrorCode::getMessage(ErrorCode::GRPC_RPC_UNKNOWN_ERROR);
            }
        }

        Log::get()->error('远程服务调用异常', [
            'method' => $method,
            'argument' => $argument,
            'deserialize' => $deserialize,
            'metadata' => $metadata,
            'options' => $options,
            'response' => $response,
            'status' => $status,
        ]);

        throw new GeneralException(ErrorCode::GRPC_RPC_SERVER_ERROR, $message);
    }
}
