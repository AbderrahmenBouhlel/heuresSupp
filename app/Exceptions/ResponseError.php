<?php



namespace App\Exceptions;
class ResponseError{
    private array $payload ;
    private int $statusCode ;


    public function __construct() {
        $this->payload = [];
        $this->statusCode = 500 ;
    }



    // gtters and setter
    public function getPayload(): array {
        return $this->payload;
    }
    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function setPayload(array $payload): void {
        $this->payload = $payload;
    }

    public function setStatusCode(int $statusCode): void {
        $this->statusCode = $statusCode;
    }
    

}