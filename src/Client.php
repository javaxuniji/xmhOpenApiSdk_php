<?php

namespace XMH\OpenApiSdk;

use XMH\OpenApiSdk\Auth\Auth;
use XMH\OpenApiSdk\Exception\ErrorCodes;
use XMH\OpenApiSdk\Exception\OpenApiException;
use XMH\OpenApiSdk\Exception\OpenApiRetryableException;
use XMH\OpenApiSdk\Exception\OpenApiTimeOutException;
use XMH\OpenApiSdk\Exception\SdkException;

class Client {
    private Auth $auth;
    private LoggerInterface $logger;
    private StorageInterface $storage;
    private string $env;
    private string $signSalt;
    private string $appId;
    private string $appSecret;

    private int $maxRetry;
    private BaseClient $baseClient;

    public function __construct() {
        $this->appId = XmhConfig::getAppId();
        $this->appSecret = XmhConfig::getAppSecret();
        $this->signSalt = XmhConfig::getSignSalt();
        $this->logger = XmhConfig::getLogger();
        $this->storage = XmhConfig::getStorage();
        $this->auth = new Auth();
        $this->env = XmhConfig::getEnv();
        switch ($this->env) {
            case    XmhCommon::ENV_ALPHA:
                $this->doamin = XmhCommon::DOMAIN_ALPHA;
                $this->baseurl = XmhCommon::BASEURL_ALPHA;
                break;
            case XmhCommon::ENV_BETA:
                $this->doamin = XmhCommon::DOMAIN_BETA;
                $this->baseurl = XmhCommon::BASEURL_BETA;
                break;
            case XmhCommon::ENV_IDC:
                $this->doamin = XmhCommon::DOMAIN_IDC;
                $this->baseurl = XmhCommon::BASEURL_IDC;
                break;
        }
        $this->baseClient = new BaseClient();
        $this->maxRetry = 3;
    }

    public function getAuth(): Auth {
        return $this->auth;
    }

    public function getLogger(): LoggerInterface {
        return $this->logger;
    }

    public function getStorage(): StorageInterface {
        return $this->storage;
    }

    /**
     * @param $url
     * @param $params
     * @return array
     * @throws SdkException
     */
    public function baseDo($url, $params): array {
        $jsonParams = json_encode($params, 256);
        $this->logger->debug("baseDo input params:{$jsonParams}");
        $authorization = $this->auth->getAccessToken();
        $binaryHash = hash_hmac('sha256', $jsonParams, $this->signSalt);
        $signature = base64_encode($binaryHash);
        $callUrl = "https://{$this->doamin}/$this->baseurl/$url";
        $this->logger->debug("call url $callUrl");
//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => $callUrl,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 60,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => $jsonParams,
//            CURLOPT_SSL_VERIFYPEER => false,
//            CURLOPT_HTTPHEADER => array(
//                "Authorization:Bearer $authorization",
//                "ApiVersion: 20250411",
//                "ClientVersion:20250411",
//                "Signature: $signature",
//                "X-App-Id:{$this->appId}",
//                "Content-Type: application/json",
//            ),
//        ));
//
//        $response = curl_exec($curl);
//        if (curl_errno($curl)) {
//            curl_close($curl);
//            $this->logger->error("curl call ${url} error");
//            throw  OpenApiException::fromErrorInfo(ErrorCodes::$OPRNAPI_REQUEST_ERROR);
//        } else {
//            $httpInfo = curl_getinfo($curl);
//            $http_code = $httpInfo['http_code'];
//            if ($http_code != 200) {
//                curl_close($curl);
//                $this->logger->error("curl call ${url} error http status:{$http_code} is not 200 ok");
//                if (in_array($http_code, [
//                    408, 503, 504, 598, 599// time out
//                ])) throw  OpenApiTimeOutException::fromErrorInfo(ErrorCodes::$NETWORK_TIME_OUT_ERROR);
////                if (in_array($http_code, [
////                    408, 503, 504, 598, 599
////                ])) throw  OpenApiException::fromErrorInfo(ErrorCodes::$NETWORK_ERROR);
//                throw  OpenApiException::fromErrorInfo(ErrorCodes::$OPRNAPI_REQUEST_ERROR);
//            }
//        }
//        curl_close($curl);
//        $resData = json_decode($response, true);
        $fullBaseUrl=XmhConfig::GetFullBaseUri();
        $response = $this->baseClient->do('POST', "$fullBaseUrl/$url", array(
                "Authorization:Bearer $authorization",
                "ApiVersion: 20250411",
                "ClientVersion:20250411",
                "Signature: $signature",
                "X-App-Id:{$this->appId}",
                "Content-Type: application/json",
            ), json_encode($params));
        if ($response === null) {
            throw  OpenApiException::fromErrorInfo(ErrorCodes::$OPENAPI_RESULT_EMPTY);
        }
        $apiErrCode = $response['errCode'];
        $apiErrMsg = $response['errMsg'];
        $data = $response['data'];
        if ($apiErrCode !== 0) {
            if (in_array($apiErrCode, [720402,
                720404])) {
                $this->auth->refreshToken();
            }
            if (in_array($apiErrCode, [ //Retryable error
                620006,
                720402,
                720404
            ])) throw new OpenApiRetryableException($apiErrMsg, $apiErrCode, $data);
            throw  new OpenApiException($apiErrMsg, $apiErrCode, $data);
        }
        return $data;
    }

    /**
     * @param $url
     * @param $params
     * @return array
     * @throws SdkException
     */
    public function do($url, $params): array {
        for ($i = 0; $i < $this->maxRetry; $i++) {
            try {
                return $this->baseDo($url, $params);
            } catch (OpenApiRetryableException $except) {
                $this->logger->error("do {$url} error {$except->getMessage()}");
                if (in_array($except->getCode(), [  //retryable error
                    ErrorCodes::$NETWORK_TIME_OUT_ERROR->getCode()
                ])) {
                    continue; //retry
                }
            }
        }
    }
}