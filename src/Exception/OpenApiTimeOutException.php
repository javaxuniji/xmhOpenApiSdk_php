<?php

namespace XMH\OpenApiSdk\Exception;

class OpenApiTimeOutException extends OpenApiRetryableException {
    public function __construct(string $message = "", int $code = 0, ?array $responseData = null, ?\Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}