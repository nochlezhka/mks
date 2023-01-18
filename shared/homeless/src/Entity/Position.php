<?php

namespace App\Entity;

use App\Repository\PositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Должность пользователя
 */
#[ORM\Entity(repositoryClass: PositionRepository::class)]
class Position extends BaseEntity
{
    /**
     * Название
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $name = null;

    /**
     * Пользователи с данной должностью
     */
    #[ORM\OneToMany(mappedBy: "position", targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return Position
     */
    public function setName(?string $name): Position
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Add user
     *
     * @param User $user
     *
     * @return Position
     */
    public function addUser(User $user): Position
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
