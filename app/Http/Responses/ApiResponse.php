<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use illuminate\Contracts\Support\Responsable;

class ApiResponse implements Responsable
{
    protected int $httpStatusCode;
    protected mixed $data;
    protected string $errorMessage;
    protected array $meta;
    protected ?string $message = null;

    public function __construct(
        int $httpStatusCode,
        mixed $data = [],
        string $message = '',
        array $meta = [],
        string $errorMessage = '',
    ) {
        
        if(!(($httpStatusCode >= 200 && $httpStatusCode <= 299) || ($httpStatusCode >= 400 && $httpStatusCode <= 599)))
        {
            throw new \RuntimeException($httpStatusCode . ' is not a valid HTTP status code.');
        }

        $this->httpStatusCode = $httpStatusCode;
        $this->data = $data;
        $this->message = $message;
        $this->meta = $meta;
        $this->errorMessage = $errorMessage;
    }

    public function toResponse($request)
    {
        $payload = match (true) {

            $this->httpStatusCode >= 500 => [
                'error_message' => $this->errorMessage ?: 'Server error occurred, Please try again later.',
                'status' => $this->httpStatusCode,
            ],

            $this->httpStatusCode >= 400 => [
                'error_message' => $this->errorMessage ?: 'An error occurred.',
                'status' => $this->httpStatusCode,
            ],

            $this->httpStatusCode >= 200 => array_filter([
                'message' => $this->message,
                'data' => $this->data,
                'meta' => $this->meta ?: null,
            ], fn ($value) => $value !== null),

            default => ['message' => 'Unknown response'],
        };

        return response()->json(
            data: $payload,
            status: $this->httpStatusCode,
            options: JSON_UNESCAPED_UNICODE
        );
    }

    // Success Responses (2xx)
    public static function ok (mixed $data = [] , string $message = 'OK' , array $meta = []): self
    {
        return new static(200, $data, $message, $meta);
    }

    public static function created (mixed $data = [] , string $message = 'Created successfully' , array $meta = []): self
    {
    return new static(201, $data, $message, $meta);
    }

    public static function accepted (mixed $data = [] , string $message = 'Accepted' , array $meta = []): self
    {
        return new static(202, $data, $message, $meta);
    }

    public static function noContent (): self
    {
        return new static (204 , [] , '');
    }


    // Client Responses(4xx)
    public static function badRequest(mixed $data = [], string $message = 'Bad request', array $meta = []): self 
    {
        return new static(400, $data, $message, $meta);
    }

    public static function unauthorized(mixed $data = [], string $message = 'Unauthorized', array $meta = []): self 
    {
        return new static(401, $data, $message, $meta);
    }

    public static function forbidden(mixed $data = [] ,string $message = 'Forbidden', array $meta = []): self 
    {
        return new static(403, $data, $message, $meta);
    }

    public static function notFound(mixed $data = [], string $message = 'Resource not found', array $meta = []): self
    {
        return new static(404, $data, $message, $meta);
    }

    public static function conflict(mixed $data = [], string $message = 'Conflict occurred', array $meta = []): self 
    {
        return new static(409, $data, $message, $meta);
    }

    public static function unprocessable(mixed $data = [], string $message = 'Unprocessable entity', array $meta = []): self 
    {
        return new static(422, $data, $message, $meta);
    }

    public static function tooManyRequests(mixed $data = [], string $message = 'Too many requests' , array $meta = []): self 
    {
        return new static(429, $data, $message, $meta);
    }
}