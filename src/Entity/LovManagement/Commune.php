<?php

namespace App\Entity\LovManagement;

use App\Model\Lov as baseLov;
use Doctrine\ORM\Mapping as ORM;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Entity\LovManagement\Ville;
use App\Entity\LovManagement\Zone;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LovManagement\CommuneRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Audit\Auditable
 * @Audit\Security(view={"ROLE_ADMIN"})
 */
class Commune extends baseLov
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="zones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ville;

    /**
     * @ORM\OneToMany(targetEntity=Zone::class, mappedBy="ville")
     */
    private $zones;

    public function __construct()
    {
        parent::__construct();
        $this->zones = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
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

     public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
}
