<?php


namespace XMH\OpenApiSdk\Exception;

class Errors {
    private int $code;
    private string $message;

    public function __construct(string $message = "", int $code = 0) {
        $this->message = $message;
        $this->code = $code;
    }

    public function getCode(): int {
        return $this->code;
    }

    public function setCode(int $code): self {
        $this->code = $code;
        return $this;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function setMessage(string $message): self {
        $this->message = $message;
        return $this;
    }
}


class SdkException extends \Exception {
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public static function fromErrorInfo(Errors $errorInfo, ?\Throwable $previous = null): self {
        return new static($errorInfo->getMessage(), $errorInfo->getCode(), $previous);
    }
}


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

/**
 * retryable exception
 */
class OpenApiRetryableException extends SdkException {
    public function __construct(string $message = "", int $code = 0, ?array $responseData = null, ?\Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
class OpenApiTimeOutException extends OpenApiRetryableException {
    public function __construct(string $message = "", int $code = 0, ?array $responseData = null, ?\Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

ErrorCodes::init();