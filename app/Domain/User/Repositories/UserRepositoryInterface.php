<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;

interface UserRepositoryInterface {

    public function create(User $user): User;
    public function update(User $user): User;
    public function delete(int $id): void;
    public function getAll(): array;
    public function findById(int $id): ?User;
    public function findByUserName(string $userName): ?User;
    
}