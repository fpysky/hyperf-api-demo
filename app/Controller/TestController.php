<?php

declare(strict_types=1);

namespace App\Controller;

use App\Extend\Log\Log;
use App\GrpcClient\TestClient;
use Grpc\Req;
use Grpc\TokenTestResult;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class TestController extends AbstractController
{
    public function test(): array
    {
        $client = new TestClient();

        $token = $this->getToken();
        $req = new Req();
        $req->setToken($token);

        /** @var TokenTestResult $result */
        [$result, $status] = $client->tokenTest($req);

        return [
            'data' => [
                'token' => $result->getToken(),
                'requestStatus' => $status,
            ],
            'message' => 'ok!',
            'code' => 200000,
        ];
    }

    private function checkToken(): array
    {
        $baseUrl = 'http://172.29.151.155:8000';
        $loginUri = '/oauth/check_token';
        $client = new Client(['base_uri' => $baseUrl]);
        $contents = [];
        try {
            $response = $client->request('GET', $loginUri, [
                RequestOptions::QUERY => [
                    'token' => 'b022a5fa-2335-4783-bbf4-1be17fc461be',
                ],
            ]);
            $contents = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::get()->error('登陆校验失败', ['message' => $e->getMessage()]);
        }
        return $contents;
    }

    private function getToken()
    {
        $contents = $this->login();
        if (isset($contents['data']['access_token'])) {
            return $contents['data']['access_token'];
        }
        throw new \RuntimeException('登陆失败');
    }

    private function login(): array
    {
        $baseUrl = 'http://172.29.151.155:8000';
        $loginUri = '/auth/oauth/token';
        $client = new Client(['base_uri' => $baseUrl]);
        $contents = [];
        try {
            $response = $client->request('POST', $loginUri, [
                RequestOptions::FORM_PARAMS => [
                    'username' => '15103636900',
                    'password' => 'admin123',
                    'grant_type' => 'password',
                    'client_id' => 'qy_client',
                    'client_secret' => 'qy@12345',
                ],
            ]);
            $contents = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::get()->error('登陆失败', ['message' => $e->getMessage()]);
        }
        return $contents;
    }
}
