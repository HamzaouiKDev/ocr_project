<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypePageOcr
 *
 * @ORM\Table(name="type_page_ocr")
 * @ORM\Entity
 */
class TypePageOcr
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="path", type="text", length=65535, nullable=true)
     */
    private $path;

    /**
     * @var string|null
     *
     * @ORM\Column(name="matricule", type="text", length=65535, nullable=true)
     */
    private $matricule;

    /**
     * @var string|null
     *
     * @ORM\Column(name="annee", type="text", length=65535, nullable=true)
     */
    private $annee;

    /**
     * @var int|null
     *
     * @ORM\Column(name="page", type="bigint", nullable=true)
     */
    private $page;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TYPE", type="text", length=65535, nullable=true)
     */
    private $type;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Nbr", type="bigint", nullable=true)
     */
    private $nbr;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Label_type", type="text", length=65535, nullable=true)
     */
    private $labelType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(?string $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(?string $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNbr(): ?string
    {
        return $this->nbr;
    }

    public function setNbr(?string $nbr): self
    {
        $this->nbr = $nbr;

        return $this;
    }

    public function getLabelType(): ?string
    {
        return $this->labelType;
    }

    public function setLabelType(?string $labelType): self
    {
        $this->labelType = $labelType;

        return $this;
    }


}
