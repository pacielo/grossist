<?php

namespace App\Entity\LovManagement;

use App\Model\Lov as baseLov;
use Doctrine\ORM\Mapping as ORM;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Entity\LovManagement\Commune;
use App\Entity\LovManagement\Quartier;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LovManagement\ZoneRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Audit\Auditable
 * @Audit\Security(view={"ROLE_ADMIN"})
 */
class Zone extends baseLov
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Commune::class, inversedBy="zones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commune;

    /**
     * @ORM\OneToMany(targetEntity=Quartier::class, mappedBy="zone")
     */
    private $quartiers;

    public function __construct()
    {
        parent::__construct();
        $this->quartiers = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

        return $this;
    }


    /**
     * @return Collection|Quartier[]
     */
    public function getQuartiers()
    {
        return $this->quartiers;
    }

    public function addQuartiers(Quartier $quartier): self
    {
        if (!$this->quartiers->contains($quartier)) {
            $this->quartiers[] = $quartier;
            $quartier->setZone($this);
        }

        return $this;
    }

    public function removeQuartiers(Quartier $quartier): self
    {
        if ($this->quartiers->removeElement($quartier)) {
            if ($quartier->getZone() === $this) {
                $quartier->setZone(null);
            }
        }

        return $this;
    }

}
