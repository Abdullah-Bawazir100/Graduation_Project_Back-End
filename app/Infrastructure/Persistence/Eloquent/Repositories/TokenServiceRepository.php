<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\User\Interfaces\TokenServiceInterface;
use App\Domain\User\Entities\User as DomainUser;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Laravel\Sanctum\PersonalAccessToken;

class TokenServiceRepository implements TokenServiceInterface {

    public function generateToken(DomainUser $user): string
    {
        $user = UserModel::findOrFail($user->id);
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function removeToken(string $token)
    {
        $accessToken = PersonalAccessToken::findToken($token);
        $accessToken->delete();
    }

}
