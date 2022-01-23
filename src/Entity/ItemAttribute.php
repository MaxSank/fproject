<?php

namespace App\Entity;

use App\Repository\ItemAttributeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemAttributeRepository::class)]
class ItemAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'itemAttributes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $item;

    #[ORM\ManyToOne(targetEntity: ItemCollectionAttribute::class, inversedBy: 'itemAttributes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $itemCollectionAttribute;

    #[ORM\Column(type: 'string', length: 500)]
    private $value;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function setItem(Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getItemCollectionAttribute(): ItemCollectionAttribute
    {
        return $this->itemCollectionAttribute;
    }

    public function setItemCollectionAttribute(ItemCollectionAttribute $itemCollectionAttribute): self
    {
        $this->itemCollectionAttribute = $itemCollectionAttribute;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
