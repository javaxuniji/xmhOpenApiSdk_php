<?php

namespace XMH\OpenApiSdk\Exception;

class OpenApiException extends SdkException {
    private ?array $responseData = null;

    public function __construct(string $message = "", int $code = 0, ?array $responseData = null, ?\Throwable $previous = null) {
        $this->responseData = $responseData;
        parent::__construct($message, $code, $previous);
    }

    public function getResponseData(): ?array {
        return $this->responseData;
    }

    /**
     * 从ErrorInfo创建OpenAPI异常
     */
    public static function fromErrorInfo(Errors $errorInfo, ?array $responseData = null, ?\Throwable $previous = null): self {
        return new static($errorInfo->getMessage(), $errorInfo->getCode(), $responseData, $previous);
    }
}