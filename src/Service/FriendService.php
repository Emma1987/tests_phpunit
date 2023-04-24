<?php

namespace App\Service;

use App\Entity\Enum\FriendType;
use App\Repository\FriendRepository;
use Greg0ire\Enum\Bridge\Symfony\Translator\GetLabel;
use Symfony\Contracts\Translation\TranslatorInterface;

class FriendService
{
    public function __construct(private FriendRepository $friendRepository, private TranslatorInterface $translator) {}

    public function getAllMyFriendsAsArray(): array
    {
        $label = new GetLabel($this->translator);
        $friendsFromDb = $this->friendRepository->findAll();

        $friends = [];
        foreach ($friendsFromDb as $friendFromDb) {
            $friends[$friendFromDb->getName()] = ($label)($friendFromDb->getType(), FriendType::class, 'enum');
        }

        return $friends;
    }
}
