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
        $this->token = config('services.inbox.token', '');
    }

    public function sendMessage(string $phone, string $message)
    {
        $phone = $this->formatPhone($phone);

        try{

            $response = Http::withoutVerifying()
            ->post($this->apiUrl, [
                'token' => $this->token,
                'number' => $phone,
                'message' => $message,
            ]);

            Log::info('WhatsApp API Response', ['response' => $response->json()]);

            if($response->successful())
            {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('فشل في ارسال الرسالة' , [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => $response->json('message') ?? 'فشل في ارسال الرسالة',
            ];

        } catch (\Exception $e){
            Log::error('فشل في ارسال الرسالة' , [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Server error connected to WhatsApp Gateway',
            ];
        }
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^\d+]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        if (!str_starts_with($phone, '967') && !str_starts_with($phone, '+967')) {
            $phone = '+967' . $phone;
        } elseif (str_starts_with($phone, '967')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }
}
