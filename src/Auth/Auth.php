<?php

namespace XMH\OpenApiSdk\Auth;

use XMH\OpenApiSdk\BaseClient;
use XMH\OpenApiSdk\Exception\ErrorCodes;
use XMH\OpenApiSdk\Exception\OpenApiException;
use XMH\OpenApiSdk\Exception\SdkException;
use XMH\OpenApiSdk\StorageInterface;
use XMH\OpenApiSdk\Config;

class Auth {
    private BaseClient $baseClient;
    const XMH_TOKEN_STORAGE_KEY = 'XMH_TOKEN_STORAGE_KEY';

    public function __construct() {
        $this->baseClient = new BaseClient();
    }

    /**
     * @throws OpenApiException
     * @throws SdkException
     */
    public function getAccessToken(): string {
        $token = Config::getStorage()->get(self::XMH_TOKEN_STORAGE_KEY);
        if (!empty($token)) {
            return $token;
        }
        $appId = Config::getAppId();
        $appSecret = Config::getAppSecret();
        $fullBaseUri = Config::GetFullBaseUri();

        $response = $this->baseClient->do('POST', "$fullBaseUri/applyToken", array(
            "ApiVersion: 20250411",
            "ClientVersion:20250411",
            "X-App-Id:{$appId}",
            "Content-Type: application/json",
        ), json_encode([
            'appId' => $appId,
            'appSecret' => $appSecret
        ]));

        if ($response === null) {
            throw  OpenApiException::fromErrorInfo(ErrorCodes::$OPENAPI_RESULT_EMPTY);
        }
        $apiErrCode = $response['errCode'];
        $apiErrMsg = $response['errMsg'];
        $data = $response['data'];
        if ($apiErrCode !== 0) {
            throw  new OpenApiException($apiErrMsg, $apiErrCode, $data);
        }
        if (!isset($data['access_token'])) {
            throw new SdkException('Failed to get access token');
        }

        Config::getStorage()->set(self::XMH_TOKEN_STORAGE_KEY, $data['accessToken'], $data['expiredSecond'] ?? 3600);
        return $data['accessToken'];
    }

    /**
     * @throws OpenApiException
     * @throws SdkException
     */
    public function refreshToken(): void {
        Config::getStorage()->delete(self::XMH_TOKEN_STORAGE_KEY);
        $this->getAccessToken();
    }
}