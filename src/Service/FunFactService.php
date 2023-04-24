<?php

namespace App\Service;

use App\Repository\FunFactRepository;

class FunFactService
{
    public function __construct(private FunFactRepository $repository) {}

    public function findAllFunFactsOrderedByFriendTypeAndContentAsc(): array
    {
        return $this->repository->findAllFunFactsOrderedByFriendTypeAndContentAsc();
    }
}
