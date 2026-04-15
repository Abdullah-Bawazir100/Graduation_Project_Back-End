<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],

            'user' => [
                'first_name' => $this['user']['first_name'] ?? null,
                'last_name'  => $this['user']['last_name'] ?? null,
                'name'       => $this['user']['name'] ?? 'نظام',
                'role'       => $this['user']['role'] ?? '—',
            ],

            'action' => $this['action'],

            'model' => [
                'id'   => $this['model']['id'] ?? null,
                'name' => $this['model']['name'] ?? null,
            ],

            'details' => $this['details'],

            'datetime' => $this['datetime'],
        ];
    }
}
