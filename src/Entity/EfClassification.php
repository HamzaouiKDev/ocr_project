<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EfClassification
 *
 * @ORM\Table(name="ef_classification")
 * @ORM\Entity
 */
class EfClassification
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
     * @ORM\Column(name="Code", type="text", length=65535, nullable=true)
     */
    private $code;

    /**
     * @var float|null
     *
     * @ORM\Column(name="CODE_CLASSE", type="float", precision=10, scale=0, nullable=true)
     */
    private $codeClasse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="label", type="text", length=65535, nullable=true)
     */
    private $label;

    /**
     * @var string|null
     *
     * @ORM\Column(name="label_recherche", type="text", length=65535, nullable=true)
     */
    private $labelRecherche;

    /**
     * @var int|null
     *
     * @ORM\Column(name="INDIC_TRAITEMENT", type="bigint", nullable=true)
     */
    private $indicTraitement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type_EF", type="text", length=65535, nullable=true)
     */
    private $typeEf;

    /**
     * @var int|null
     *
     * @ORM\Column(name="NIV_TYPE", type="bigint", nullable=true)
     */
    private $nivType;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCodeClasse(): ?float
    {
        return $this->codeClasse;
    }

    public function setCodeClasse(?float $codeClasse): self
    {
        $this->codeClasse = $codeClasse;

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

    public function getLabelRecherche(): ?string
    {
        return $this->labelRecherche;
    }

    public function setLabelRecherche(?string $labelRecherche): self
    {
        $this->labelRecherche = $labelRecherche;

        return $this;
    }

    public function getIndicTraitement(): ?string
    {
        return $this->indicTraitement;
    }

    public function setIndicTraitement(?string $indicTraitement): self
    {
        $this->indicTraitement = $indicTraitement;

        return $this;
    }

    public function getTypeEf(): ?string
    {
        return $this->typeEf;
    }

    public function setTypeEf(?string $typeEf): self
    {
        $this->typeEf = $typeEf;

        return $this;
    }

    public function getNivType(): ?string
    {
        return $this->nivType;
    }

    public function setNivType(?string $nivType): self
    {
        $this->nivType = $nivType;

        return $this;
    }


}
