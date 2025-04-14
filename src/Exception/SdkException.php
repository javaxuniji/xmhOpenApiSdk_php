<?php

namespace XMH\OpenApiSdk\Exception;

class SdkException extends \Exception {
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public static function fromErrorInfo(Errors $errorInfo, ?\Throwable $previous = null): self {
        return new static($errorInfo->getMessage(), $errorInfo->getCode(), $previous);
    }
}