<?php

namespace XMH\OpenApiSdk\Exception;

class ErrorCodes {
    public static $OPENAPI_RESULT_EMPTY;
    public static $NETWORK_TIME_OUT_ERROR;
    public static $NETWORK_ERROR;
    public static $OPRNAPI_REQUEST_ERROR;

    public static $INVALID_PARAM;

    public static function init() {
        self::$OPENAPI_RESULT_EMPTY = new Errors('OpenAPI result empty', 1000);
        self::$NETWORK_TIME_OUT_ERROR = new Errors('Network TimeOut', 1001);
        self::$NETWORK_ERROR = new Errors('Network Error', 1002);
        self::$OPRNAPI_REQUEST_ERROR = new Errors('Openapi Request Error', 10);

        self::$INVALID_PARAM = new Errors('Invalid parameter', 1002);
    }
}