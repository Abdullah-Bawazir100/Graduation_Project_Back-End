<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use DomainException;
/*
class CompleteProfileUseCase {

    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {}

    public function execute(User $user, array $profileData){

        if ($user->mustChangePassword) {
            throw new DomainException('Unauthorized: User must change password before completing profile.');
        }

        if ($user->isProfileCompleted) {
            throw new DomainException('Profile is already completed.');
        }

        if (!empty($profileData['dateOfBirth'])) {
            try {
                $user->dateOfBirth = new \DateTime($profileData['dateOfBirth']);
            } catch (\Exception $e) {
                throw new DomainException('Invalid date format for dateOfBirth.');
            }
        }

        $user->idCard = $profileData['idCard'] ?? $user->idCard;
        $user->phone = $profileData['phone'] ?? $user->phone;

        $user->isProfileCompleted = true;

        return $this->userRepository->update($user);

    }
} */
