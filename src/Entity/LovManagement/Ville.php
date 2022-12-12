<?php

namespace App\Entity\LovManagement;

use App\Entity\TransportManagement\Vehicule;
use App\Model\Lov as baseLov;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Entity\LovManagement\Country;
use App\Entity\LovManagement\Zone;
use App\Entity\LovManagement\Commune;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LovManagement\VilleRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Audit\Auditable
 * @Audit\Security(view={"ROLE_ADMIN"})
 */
class Ville extends baseLov
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="villes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @ORM\ManyToMany(targetEntity=Vehicule::class, mappedBy="ville")
     */
    private $vehicules;

    /**
     * @ORM\OneToMany(targetEntity=Commune::class, mappedBy="ville")
     */
    private $communes;

    public function __construct()
    {
        parent::__construct();
        $this->vehicules = new ArrayCollection();
        $this->communes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Commune[]
     */
    public function getVehicules(): Collection
    {
        return $this->communes;
    }

    public function addCommunes(Commune $commune): self
    {
        if (!$this->communes->contains($commune)) {
            $this->communes[] = $commune;
            $commune->addVille($this);
        }

        return $this;
    }

    public function removeCommunes(Commune $commune): self
    {
        if ($this->communes->removeElement($commune)) {
            $commune->removeVille($this);
        }

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection|Zone[]
     */
    public function getZones(): Collection
    {
        return $this->zones;
    }

    public function addZones(Zone $zone): self
    {
        if (!$this->zones->contains($zone)) {
            $this->zones[] = $zone;
            $zone->setVille($this);
        }

        return $this;
    }

    public function removeZones(Zone $zone): self
    {
        if ($this->zones->removeElement($zone)) {
            if ($zone->getVille() === $this) {
                $zone->setVille(null);
            }
        }

        return $this;
    }
}
