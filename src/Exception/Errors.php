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


ErrorCodes::init();