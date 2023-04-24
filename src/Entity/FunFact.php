<?php

namespace App\Entity;

use App\Entity\Enum\FriendType;
use App\Repository\FunFactRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FunFactRepository::class)]
class FunFact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(length: 50)]
    private ?string $friendType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getFriendType(): ?string
    {
        return $this->friendType;
    }

    public function setFriendType(string $friendType): self
    {
        FriendType::assertValidValue($friendType);
        $this->friendType = $friendType;

        return $this;
    }
}
