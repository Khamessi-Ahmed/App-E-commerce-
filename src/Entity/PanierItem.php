<?php

namespace App\Entity;

use App\Repository\PanierItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: PanierItemRepository::class)]
#[Broadcast]
class PanierItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Livres $livre = null;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: true)]
    private ?int $quantite = null;

    // #[ORM\ManyToOne(inversedBy: 'PanierItems')]
    #[ORM\ManyToOne(targetEntity:Panier::class, inversedBy:"PanierItems", cascade:["persist"])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Panier $panier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLivre(): ?Livres
    {
        return $this->livre;
    }

    public function setLivre(?Livres $livre): static
    {
        $this->livre = $livre;
        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): static
    {
        $this->panier = $panier;

        return $this;
    }

    public function toAchat(): Achat
    {
        $achat = new Achat();
        $achat->setLivre($this->getLivre());
        $achat->setQte($this->getQuantite());
        $this->setLivre($this->getLivre());
        $this->setQuantite(0);

        return $achat;
    }
}
