<?php

namespace App\Application\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $token;

    public function __construct()
    {
        $this->apiUrl = config('services.inbox.url', '');
        $this->token  = config('services.inbox.token', '');
    }

    public function sendMessage(string $phone, string $message): array
    {
        $phone = $this->formatPhone($phone);

        try {
            $response = Http::withoutVerifying()
                ->post($this->apiUrl . '?token=' . $this->token, [
                    'number'  => $phone,
                    'message' => $message,
                ]);

            Log::info('WhatsApp Request', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->failed()) {
                Log::error('WhatsApp Send Failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'error' => $response->json('message')
                        ?? 'Failed to send WhatsApp message',
                ];
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];

        } catch (\Throwable $e) {
            Log::error('WhatsApp Exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Server error while sending WhatsApp message',
            ];
        }
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        if (!str_starts_with($phone, '967')) {
            $phone = '967' . $phone;
        }

        return '+' . $phone;
    }
}
