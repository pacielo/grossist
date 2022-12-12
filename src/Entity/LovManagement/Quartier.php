<?php

namespace App\Entity\LovManagement;

use App\Entity\TransportManagement\Vehicule;
use App\Model\Lov as baseLov;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Entity\LovManagement\Zone;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LovManagement\QuartierRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Audit\Auditable
 * @Audit\Security(view={"ROLE_ADMIN"})
 */
class Quartier extends baseLov
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Vehicule::class, mappedBy="quartier")
     */
    private $vehicules;

    /**
     * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="quartiers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $zone;

    public function __construct()
    {
        parent::__construct();
        $this->vehicules = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Vehicule[]
     */
    public function getCharge(): Collection
    {
        return $this->charge;
    }

    public function addCharge(Vehicule $vehicule): self
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules[] = $vehicule;
            $vehicule->addQuartier($this);
        }

        return $this;
    }

    public function removeCharge(Vehicule $vehicule): self
    {
        if ($this->vehicules->removeElement($vehicule)) {
            $vehicule->removeQuartier($this);
        }

        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }
}
