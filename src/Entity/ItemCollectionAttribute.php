<?php

namespace App\Entity;

use App\Repository\ItemCollectionAttributeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemCollectionAttributeRepository::class)]
class ItemCollectionAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ItemCollection::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ItemCollection $itemCollection;

    #[ORM\Column(type: 'string', length: 180)]
    private string $name;

    #[ORM\Column(type: 'string', length: 180)]
    private string $attributeType;

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

    public function getAttributeType(): string
    {
        return $this->attributeType;
    }

    public function setAttributeType(string $attributeType): self
    {
        $this->attributeType = $attributeType;

        return $this;
    }
}
