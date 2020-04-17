<?php

namespace App\Stefanwiegmann\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Stefanwiegmann\UserBundle\Repository\RoleRepository")
 * @ORM\Table(name="sw_user_role")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\Loggable(logEntryClass="App\Stefanwiegmann\UserBundle\Entity\UserLogEntry")
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $translationKey;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sort;

    /**
     * @ORM\ManyToMany(targetEntity="App\Stefanwiegmann\UserBundle\Entity\User", mappedBy="userRoles")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Stefanwiegmann\UserBundle\Entity\Group", mappedBy="groupRoles")
     */
    private $groups;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->sort = 10000;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCanonical()
    {
        $this->translationKey = 'name.'.strtolower($this->name);
        $this->slug = strtolower(str_replace('_','-',str_replace('ROLE_','',$this->name)));
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

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
