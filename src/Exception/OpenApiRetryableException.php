<?php

namespace XMH\OpenApiSdk\Exception;

/**
 * retryable exception
 */
class OpenApiRetryableException extends SdkException {
    public function __construct(string $message = "", int $code = 0, ?array $responseData = null, ?\Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}