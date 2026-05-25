<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;

interface UserRepositoryInterface {

    public function create(User $user): User;
    public function update(User $user): User;
    public function delete(int $id): void;
    public function getAll(?string $search = null): array;
    public function findById(int $id): ?User;
    public function findByUserName(string $userName): ?User;
    public function findTaxPayerById(int $id);
    public function updatePasswordOnly(int $id , string $newPassword , bool $mustChangePassword);
    public function updatePassword(int $id , string $newPassword , bool $mustChangePassword);
    public function findByUserNameAndPhone(string $userName, string $phone): ?User;
    public function countUsers(): int;
}
