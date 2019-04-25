<?php

namespace App\Stefanwiegmann\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Stefanwiegmann\UserBundle\Repository\RoleRepository")
 * @ORM\Table(name="sw_user_role")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $translationKey;

    /**
     * @ORM\ManyToMany(targetEntity="App\Stefanwiegmann\UserBundle\Entity\User", mappedBy="UserRole")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Stefanwiegmann\UserBundle\Entity\Group", mappedBy="GroupRole")
     */
    private $groups;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTranslationKey(): ?string
    {
        return $this->translationKey;
    }

    public function setTranslationKey(string $translationKey): self
    {
        $this->translationKey = $translationKey;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addUserRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeUserRole($this);
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addGroupRole($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeGroupRole($this);
        }

        return $this;
    }
}
