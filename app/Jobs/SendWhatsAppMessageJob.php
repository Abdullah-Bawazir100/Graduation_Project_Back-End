<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Application\Services\WhatsAppService;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Queueable;

    protected string $phone;
    protected string $message;

    /**
     * Create a new job instance.
     */
    public function __construct(string $phone, string $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        $whatsAppService->sendMessage($this->phone, $this->message);
    }
}
