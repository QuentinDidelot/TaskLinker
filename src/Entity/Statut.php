<?php

namespace App\Entity;

use App\Enum\StatutLibelle;
use App\Repository\StatutRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatutRepository::class)]
class Statut
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private string $libelle;

    #[ORM\ManyToOne(targetEntity: Projet::class, inversedBy: 'statuts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Projet $projet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(StatutLibelle $libelle): static
    {
        $this->libelle = $libelle->value;

        return $this;
    }

    public function getStatutLibelle(): StatutLibelle
    {
        return StatutLibelle::from($this->libelle);
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): static
    {
        $this->projet = $projet;

        return $this;
    }
}
