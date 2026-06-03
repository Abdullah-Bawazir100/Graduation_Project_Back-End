<?php

namespace App\Domain\RecyclePin\Entities;

class RecyclePin
{
    public string $modelName;
    public ?string $displayName;

    public function __construct(
        public ?int $id,
        public string $type,
        public array $data,
        public int $userId,
        public ?string $createdAt = null,
        public ?array $user = null,
    ) {
        $this->modelName = class_basename($this->type);
        $this->displayName = $this->extractDisplayName($this->data);
    }

    private function extractDisplayName(array $data): ?string
    {
        $nameKeys = [
            'name', 'full_name', 'company_name', 'trade_name', 'title', 'subject',
            'file_number', 'username', 'email', 'description', 'status_name', 'address'
        ];

        foreach ($nameKeys as $key) {
            if (!empty($data[$key])) {
                return (string) $data[$key];
            }
        }

        return null;
    }
}
