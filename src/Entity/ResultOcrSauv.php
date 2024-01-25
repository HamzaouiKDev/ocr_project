<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultOcr
 *
 * @ORM\Table(name="result_ocr_sauv")
 * @ORM\Entity
 */
class ResultOcrSauv
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
     * @var float|null
     *
     * @ORM\Column(name="ligne", type="float", precision=10, scale=0, nullable=true)
     */
    private $ligne;

    /**
     * @var string|null
     *
     * @ORM\Column(name="label", type="text", length=65535, nullable=true)
     */
    private $label;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", length=65535, nullable=true)
     */
    private $notes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value_n", type="text", length=65535, nullable=true)
     */
    private $valueN;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value_n1", type="text", length=65535, nullable=true)
     */
    private $valueN1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="text", length=65535, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type_page", type="text", length=65535, nullable=true)
     */
    private $typePage;

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

    public function getLigne(): ?float
    {
        return $this->ligne;
    }

    public function setLigne(?float $ligne): self
    {
        $this->ligne = $ligne;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getValueN(): ?string
    {
        return $this->valueN;
    }

    public function setValueN(?string $valueN): self
    {
        $this->valueN = $valueN;

        return $this;
    }

    public function getValueN1(): ?string
    {
        return $this->valueN1;
    }

    public function setValueN1(?string $valueN1): self
    {
        $this->valueN1 = $valueN1;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getTypePage(): ?string
    {
        return $this->typePage;
    }

    public function setTypePage(?string $typePage): self
    {
        $this->typePage = $typePage;

        return $this;
    }


}
