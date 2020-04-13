<?php

namespace App\Stefanwiegmann\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Stefanwiegmann\UserBundle\Repository\GroupRepository")
 * @ORM\Table(name="sw_user_group")
 * @ORM\HasLifecycleCallbacks()
 */
class Group
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Stefanwiegmann\UserBundle\Entity\Role", inversedBy="groups")
     * @ORM\JoinTable(name="sw_user_group_role")
     */
    private $groupRole;

    /**
     * @ORM\ManyToMany(targetEntity="App\Stefanwiegmann\UserBundle\Entity\User", inversedBy="groups")
     * @ORM\JoinTable(name="sw_user_group_user")
     */
    private $user;

    public function __construct()
    {
        $this->groupRole = new ArrayCollection();
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $Name): self
    {
        $this->name = $Name;

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getGroupRole(): Collection
    {
        return $this->groupRole;
    }

    public function addGroupRole(Role $groupRole): self
    {
        if (!$this->groupRole->contains($groupRole)) {
            $this->groupRole[] = $groupRole;
        }

        return $this;
    }

    public function removeGroupRole(Role $groupRole): self
    {
        if ($this->groupRole->contains($groupRole)) {
            $this->groupRole->removeElement($groupRole);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
