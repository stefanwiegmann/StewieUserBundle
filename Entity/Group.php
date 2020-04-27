<?php

namespace Stewie\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Stewie\UserBundle\Repository\GroupRepository")
 * @ORM\Table(name="sw_user_group")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\Loggable(logEntryClass="Stewie\UserBundle\Entity\UserLogEntry")
 * @Vich\Uploadable
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
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Stewie\UserBundle\Entity\Role", inversedBy="groups")
     * @ORM\JoinTable(name="sw_user_group_role")
     */
    private $groupRoles;

    /**
     * @ORM\ManyToMany(targetEntity="Stewie\UserBundle\Entity\User", mappedBy="groups")
     */
    private $users;

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
     * @Vich\UploadableField(mapping="user_avatar_group", fileNameProperty="avatarName", size="avatarSize")
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
        $this->groupRoles = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->avatarSize = 0;
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getGroupRoles(): Collection
    {
        return $this->groupRoles;
    }

    public function addGroupRole(Role $groupRole): self
    {
        if (!$this->groupRoles->contains($groupRole)) {
            $this->groupRoles[] = $groupRole;
        }

        return $this;
    }

    public function removeGroupRole(Role $groupRole): self
    {
        if ($this->groupRoles->contains($groupRole)) {
            $this->groupRoles->removeElement($groupRole);
        }

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
            $user->addGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeGroup($this);
        }

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
}
