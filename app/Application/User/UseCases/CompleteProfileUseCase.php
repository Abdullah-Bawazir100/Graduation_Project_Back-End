<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use DomainException;

class CompleteProfileUseCase {

    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {}

    public function execute(User $user, array $profileData){

       // Check if user must change his password before completing profile
        if ($user->mustChangePassword) {
            throw new DomainException('Unauthorized: User must change password before completing profile.');
        }

        // Check if the user's profile is already completed
        if ($user->isProfileCompleted) {
            throw new DomainException('Profile is already completed.');
        }

        // Date of birth is optional, but if provided, it must be a valid date
        if (!empty($profileData['dateOfBirth'])) {
            try {
                $user->dateOfBirth = new \DateTime($profileData['dateOfBirth']);
            } catch (\Exception $e) {
                throw new DomainException('Invalid date format for dateOfBirth.');
            }
        }

        $user->idCard = $profileData['idCard'] ?? $user->idCard;
        $user->phone = $profileData['phone'] ?? $user->phone;

        // Change the profile completion status
        $user->isProfileCompleted = true;

        // Save data into database
        return $this->userRepository->update($user);

    }
}
