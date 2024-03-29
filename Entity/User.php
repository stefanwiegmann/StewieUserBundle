<?php

namespace Stewie\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Stewie\UserBundle\Repository\UserRepository")
 * @ORM\Table(name="stewie_user_user")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\Loggable(logEntryClass="Stewie\UserBundle\Entity\UserLogEntry")
 * @Vich\Uploadable
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $usernameCanonical;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $emailCanonical;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $fullNameUsername;

    /**
     * @ORM\ManyToMany(targetEntity="Stewie\UserBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="stewie_user_user_role")
     */
    private $userRoles;

    /**
     * @ORM\ManyToMany(targetEntity="Stewie\UserBundle\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="stewie_user_user_group")
     */
    private $groups;

    /**
     * @ORM\Column(type="string", length=64, nullable=true, unique=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tokenDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\ManyToOne(targetEntity="Stewie\UserBundle\Entity\User", inversedBy="invited", cascade={"persist", "remove"})
     */
    private $inviter;

    /**
     * @ORM\OneToMany(targetEntity="Stewie\UserBundle\Entity\User", mappedBy="inviter", cascade={"persist", "remove"})
     */
    private $invited;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="stewie_user_avatar", fileNameProperty="avatarName", size="avatarSize")
     * @Assert\File(
     *     maxSize = "2048k",
     *     mimeTypes = {"image/png", "image/jpeg", "image/gif"},
     *     mimeTypesMessage = "Please upload a valid JPG, GIF or PNG"
     * )
     *
     * @var File|null
     */
    private $avatarFile;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $avatarName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int|null
     */
    private $avatarSize;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $avatarFile
     */
    public function setAvatarFile(?File $avatarFile = null): void
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatarName(?string $avatarName): void
    {
        $this->avatarName = $avatarName;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarSize(?int $avatarSize): void
    {
        $this->avatarSize = $avatarSize;
    }

    public function getAvatarSize(): ?int
    {
        return $this->avatarSize;
    }

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->avatarSize = 0;
        $this->updatedAt = new \DateTimeImmutable();
        $this->invited = new ArrayCollection();

    }

    /**
     * Checks if the user is inheriting one of the roles in the given array
     *
     * @return Boolean
     */
    public function inheritedRole($element)
    {
        // Foreach group this user has
        foreach($this->getGroups() as $group)
        {
            // Foreach role this group has
            foreach($group->getGroupRoles() as $role)
            {
                    if($element == $role->getName()){
                        return true;
                    }
            }
        }

        return false;
    }

    public function getSlug(): ?string
    {
        return $this->usernameCanonical;
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function setCanonical()
    {
        $this->emailCanonical = strtolower($this->email);
        $this->usernameCanonical = strtolower($this->username);
        $this->fullNameUsername = sprintf(' %s %s (%s)',$this->firstName,$this->lastName, $this->username);
    }

     // public function clearAssociations()
     // {
     //     $this->roles = new ArrayCollection();
     //     $this->groups = new ArrayCollection();
     //     $this->userRole = new ArrayCollection();
     // }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRoles(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
        }

        return $this;
    }

    public function removeUserRoles(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
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
            $group->addUser($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeUser($this);
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenDate(): ?\DateTimeInterface
    {
        return $this->tokenDate;
    }

    public function setTokenDate(?\DateTimeInterface $tokenDate): self
    {
        $this->tokenDate = $tokenDate;

        return $this;
    }

    public function getUsernameCanonical(): ?string
    {
        return $this->usernameCanonical;
    }

    public function setUsernameCanonical(string $usernameCanonical): self
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(string $emailCanonical): self
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getInviter(): ?self
    {
        return $this->inviter;
    }

    public function setInviter(?self $inviter): self
    {
        $this->inviter = $inviter;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getInvited(): Collection
    {
        return $this->invited;
    }

    public function addInvited(User $invited): self
    {
        if (!$this->invited->contains($invited)) {
            $this->invited[] = $invited;
            $invited->setInviter($this);
        }

        return $this;
    }

    public function removeInvited(User $invited): self
    {
        if ($this->invited->contains($invited)) {
            $this->invited->removeElement($invited);
            // set the owning side to null (unless already changed)
            if ($invited->getInviter() === $this) {
                $invited->setInviter(null);
            }
        }

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getFullNameUsername(): ?string
    {
        return $this->fullNameUsername;
    }

    public function setFullNameUsername(string $fullNameUsername): self
    {
        $this->fullNameUsername = $fullNameUsername;

        return $this;
    }
}
