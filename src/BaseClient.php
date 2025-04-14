<?php

namespace XMH\OpenApiSdk;

use XMH\OpenApiSdk\Exception\ErrorCodes;
use XMH\OpenApiSdk\Exception\OpenApiException;
use XMH\OpenApiSdk\Exception\OpenApiRetryableException;
use XMH\OpenApiSdk\Exception\OpenApiTimeOutException;
use XMH\OpenApiSdk\Exception\SdkException;

class BaseClient {
    public function __construct() {
    }

    /**
     * @throws OpenApiException
     * @throws SdkException
     */
    public function do($method,$callUrl,  $headers,$params) {
        $logger = Config::getLogger();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $callUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            curl_close($curl);
            $logger->error("curl call {$callUrl} error");
            throw  OpenApiException::fromErrorInfo(ErrorCodes::$OPRNAPI_REQUEST_ERROR);
        } else {
            $httpInfo = curl_getinfo($curl);
            $http_code = $httpInfo['http_code'];
            if ($http_code != 200) {
                curl_close($curl);
                $logger->error("curl call {$callUrl} error http status:{$http_code} is not 200 ok");
                if (in_array($http_code, [
                    408, 503, 504, 598, 599// time out
                ])) throw  OpenApiTimeOutException::fromErrorInfo(ErrorCodes::$NETWORK_TIME_OUT_ERROR);
//                if (in_array($http_code, [
//                    408, 503, 504, 598, 599
//                ])) throw  OpenApiException::fromErrorInfo(ErrorCodes::$NETWORK_ERROR);
                throw  OpenApiException::fromErrorInfo(ErrorCodes::$OPRNAPI_REQUEST_ERROR);
            }
        }
        curl_close($curl);
        $resData = json_decode($response, true);
        if ($resData === null) {
            throw  OpenApiException::fromErrorInfo(ErrorCodes::$OPENAPI_RESULT_EMPTY);
        }
        return $response;
//        $apiErrCode = $resData['errCode'];
//        $apiErrMsg = $resData['errMsg'];
//        $data = $resData['data'];
//        if ($apiErrCode !== 0) {
//            if (in_array($apiErrCode, [720402,
//                720404])) {
//                $auth->refreshToken();
//            }
//            if (in_array($apiErrCode, [ //Retryable error
//                620006,
//                720402,
//                720404
//            ])) throw new OpenApiRetryableException($apiErrMsg, $apiErrCode, $data);
//            throw  new OpenApiException($apiErrMsg, $apiErrCode, $data);
//        }
    }

}