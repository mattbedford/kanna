<?php

namespace App\Module\User\List;

use App\Module\User\Data\UserData;
use App\Module\User\List\UserListFinderRepository;

final readonly class UserListFinder
{
    public function __construct(
        private UserListFinderRepository $userListFinderRepository,
    ) {
    }

    /**
     * Return all users stored in the database.
     *
     * @return UserData[]
     */
    public function findAllUsers(): array
    {
        return $this->userListFinderRepository->findAllUsers();
    }
}
