<?php

namespace App\Entity;

use App\Repository\ItemCollectionRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ItemCollectionRepository::class)]
/*#[ORM\UniqueConstraint(name: 'unique_collection', columns: ['user_id', 'name'])]*/
#[UniqueEntity(fields: ['user', 'name'], message: "There is already an collection with this name")]
class ItemCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[Assert\NotBlank(message: 'Please enter a name for the collection')]
    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[Assert\NotBlank(message: 'Please enter a description for the collection')]
    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\Column(type: 'string', length: 100)]
    private string $theme;

    #[ORM\OneToMany(mappedBy: 'itemCollection', targetEntity: Item::class, orphanRemoval: true)]
    private $items;

    #[ORM\OneToMany(mappedBy: 'itemCollection', targetEntity: ItemCollectionAttribute::class)]
    private $itemCollectionAttributes;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->itemCollectionAttributes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name.' '.$this->id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUserId(): User
    {
        return $this->user;
    }

    public function setUserId(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setItemCollection($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getItemCollection() === $this) {
                $item->setItemCollection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ItemCollectionAttribute[]
     */
    public function getItemCollectionAttributes(): Collection
    {
        return $this->itemCollectionAttributes;
    }

    public function addItemCollectionAttribute(ItemCollectionAttribute $itemCollectionAttribute): self
    {
        if (!$this->itemCollectionAttributes->contains($itemCollectionAttribute)) {
            $this->itemCollectionAttributes[] = $itemCollectionAttribute;
            $itemCollectionAttribute->setitemCollection($this);
        }

        return $this;
    }

    public function removeItemCollectionAttribute(ItemCollectionAttribute $itemCollectionAttribute): self
    {
        if ($this->itemCollectionAttributes->removeElement($itemCollectionAttribute)) {
            // set the owning side to null (unless already changed)
            if ($itemCollectionAttribute->getitemCollection() === $this) {
                $itemCollectionAttribute->setitemCollection(null);
            }
        }

        return $this;
    }
}
