<?php

namespace App\Domain\Department\ValueObjects;

class DepartmentName
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = trim($name);
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->name === '') {
            throw new \InvalidArgumentException('اسم القسم لا يمكن أن يكون فارغًا.');
        }

        if (strlen($this->name) > 255) {
            throw new \InvalidArgumentException('اسم القسم لا يمكن أن يزيد عن 255 حرفًا.');
        }
    }

    public function value(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}