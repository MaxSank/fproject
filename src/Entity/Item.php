<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ItemCollection::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ItemCollection $itemCollection;

    #[ORM\Column(type: 'string', length: 180)]
    private string $name;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: ItemAttribute::class, orphanRemoval: true)]
    private $itemAttributes;

    public function __construct()
    {
        $this->itemAttributes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getItemCollection(): ItemCollection
    {
        return $this->itemCollection;
    }

    public function setItemCollection(ItemCollection $itemCollection): self
    {
        $this->itemCollection = $itemCollection;

        return $this;
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
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getItemAttributes(): Collection
    {
        return $this->itemAttributes;
    }

    public function addItemAttribute(ItemAttribute $itemAttribute): self
    {
        if (!$this->itemAttributes->contains($itemAttribute)) {
            $this->itemAttributes[] = $itemAttribute;
            $itemAttribute->setItem($this);
        }

        return $this;
    }

    public function removeItemAttribute(ItemAttribute $itemAttribute): self
    {
        if ($this->itemAttributes->removeElement($itemAttribute)) {
            // set the owning side to null (unless already changed)
            if ($itemAttribute->getItem() === $this) {
                $itemAttribute->setItem(null);
            }
        }

        return $this;
    }
}
