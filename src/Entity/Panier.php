<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
#[Broadcast]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: PanierItem::class, mappedBy: 'panier')]
    private Collection $PanierItems;

    #[ORM\OneToOne(inversedBy: 'panier', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    public function __construct()
    {
        $this->PanierItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, PanierItem>
     */
    public function getPanierItems(): Collection
    {
        return $this->PanierItems;
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->PanierItems as $item) {
            $total += $item->getLivre()->getPrix() * $item->getQuantite();
        }
        return $total;
    }
     
    public function addPanierItem(PanierItem $panierItem): static
    {
        if (!$this->PanierItems->contains($panierItem)) {
            $this->PanierItems->add($panierItem);
            $panierItem->setPanier($this);
        }

        return $this;
    }

    public function removePanierItem(PanierItem $panierItem): static
    {
        if ($this->PanierItems->removeElement($panierItem)) {
            // set the owning side to null (unless already changed)
            if ($panierItem->getPanier() === $this) {
                $panierItem->setPanier(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }
}
