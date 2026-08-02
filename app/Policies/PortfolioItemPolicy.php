<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\PortfolioItem;
use App\Models\User;

class PortfolioItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PortfolioItem $portfolioItem): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PortfolioItem $portfolioItem): bool
    {
        return true;
    }

    public function delete(User $user, PortfolioItem $portfolioItem): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }
}
