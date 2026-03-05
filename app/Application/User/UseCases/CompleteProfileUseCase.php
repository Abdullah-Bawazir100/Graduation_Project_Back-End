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

        // Check if user is authorized to complete profile
        if ($user->mustChangePassword)
        {
            return new DomainException('Unauthorized: User must change password before completing profile.');
        }


        // Check if profile is already completed
        if($user->isProfileCompleted) {
            throw new DomainException('Profile is already completed.');
        }
        
        // Complete the profile with provided data
        $user->dateOfBirth = isset($profileData['dateOfBirth']) ? new \DateTime($profileData['dateOfBirth']) : $user->dateOfBirth; 
        $user->idCard = $profileData['idCard'] ?? $user->idCard;
        $user->phone = $profileData['phone'] ?? $user->phone;

        // Change the profile status to completed
        $user->isProfileCompleted = true;

        return $this->userRepository->update($user);

    }
}